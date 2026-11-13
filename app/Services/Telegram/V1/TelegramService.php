<?php

namespace App\Services\Telegram\V1;

use App\Services\Telegram\AbstractTelegramService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class TelegramService extends AbstractTelegramService
{
    public function getVersion(): string
    {
        return 'v1';
    }

    /**
     * Отправить сообщение в Telegram.
     *
     * @throws RequestException
     */
    public function sendMessage(string $text, ?string $chatId = null): bool
    {
        $response = $this->http()
            ->retry(3, 100, function ($exception) {
                // Повторяем только на сетевых ошибках и таймаутах,
                // не на 4xx (неверный токен/chat_id — повтор не поможет)
                return $exception instanceof ConnectionException;
            })
            ->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId ?? $this->chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ])
        ;

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json('ok', false);
    }

    public function formatMessage(array $data): string
    {
        $lines = [
            '📩 <b>Новая заявка с сайта</b>',
            '',
            '👤 <b>Имя:</b> '.e($data['username']),
            '📧 <b>Email:</b> '.e($data['email']),
        ];

        if (!empty($data['tg'])) {
            $lines[] = '💬 <b>Telegram:</b> '.e($data['tg']);
        }

        if (!empty($data['message'])) {
            $lines[] = '';
            $lines[] = '📝 <b>Сообщение:</b>';
            $lines[] = e($data['message']);
        }

        return implode("\n", $lines);
    }
}
