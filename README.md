<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Производительность

Имеем настройки по-умолчанию для PHP-FPM

```ini
listen = 127.0.0.1:9000       # TCP-сокет
pm = dynamic                  # Режим управления процессами
pm.max_children = 5           # ⚠️ максимум воркеров
pm.start_servers = 2          # стартовое количество
pm.min_spare_servers = 1
pm.max_spare_servers = 3
```

Имеем настройки по-умолчанию nginx

```yaml
worker_processes auto      # 4 ядра
worker_connections 1024
```

Тесты показали что Laravel 11 обрабатывает в синхронном режиме отправку сообщения в Telegram-бота за ~350 ms.
После включения всех доступных из коробки кэширований:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
# или
php artisan optimize

```

### `.env` для продакшена

```ini
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error

# OPcache обязателен
# (настраивается в php.ini)
```

### OPcache в php.ini

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.jit=1255
opcache.jit_buffer_size=128M
```

### Настроили Composer

```bash
composer install \
  --no-dev \
  --optimize-autoloader \
  --classmap-authoritative
```

### Итоги оптимизации

Отправка сообщения в Telegram-бота стала занимать ~200 ms.
Если превышено число запросов на API от одного IP, то запрос обрабатывается 6-10 ms. Он возвращает http `429`.

nginx может при текущих настройках выдать максиму 20480 RPS

```text
RPS ≈ worker_processes * worker_connections ​/ avg_response_time
```

Т.е. `avg_response_time` точно не может быть меньше ~200 ms.

На самом деле это значение больше и это можно увидеть при дополнительных настройках лога у nginx

```ini
http {
    log_format main '$remote_addr - $remote_user [$time_local] '
                '"$request" $status $body_bytes_sent '
                '"$http_referer" "$http_user_agent" "$http_x_forwarded_for" '
                'rt=$request_time uct=$upstream_connect_time '
                'uht=$upstream_header_time urt=$upstream_response_time';
}
server {
    access_log /var/log/nginx/access.log main;
}

```

172.21.0.2 - - [20/Jan/2026:18:19:09 +0000] "POST /api/feedback HTTP/1.1" 200 155 "-" "curl/7.68.0" "217.114.43.138" rt=0.247 uct=0.002 uht=0.248 urt=0.248

**Расшифровка таймингов**

| Метрика   | Значение | Что означает                         |
| --------- | -------- | ------------------------------------ |
| rt=0.247  | 247ms    | Общее время запроса (nginx → клиент) |
| uct=0.002 | 2ms      | Время соединения с PHP-FPM           |
| uht=0.248 | 248      | Время до первого байта от PHP        |
| urt=0.248 | 248      | Полное время ответа PHP-FPM          |

Общее время запроса немного превышает обработку отправки сообщения в Телеграм кодом в Laravel.
Но даже если мы увеличим `pm.max_children = 32`, что рекомендованный максиму для процессора в 4 ядра, мы все еще не упремся в ограничение бота Телеграм.

Лимиты можно посмотреть например тут [Ограничения в телеграм-бот (Bot API), о которых никто не расскажет](https://habr.com/ru/companies/tensor/articles/799565/)  
Много лимитов Телегам тут [Telegram Limits](https://limits.tginfo.me/en), нужен ВПН.

_30 сообщений в секунду разным пользователям от одного бота, или чаще чем раз в секунду одному._

Но даже если у нас будет процессор на 128 ядер и больше 40 Гб только под одни воркеры PHP-FPM, заниматься ожидаем ответа от бота внутри запроса, это антипаттерн.  
Поэтому нам нужна очередь, например на базе Redis.
