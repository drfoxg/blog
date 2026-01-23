<?php

namespace App\Jobs;

use App\Services\Telegram\V1\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Http\Contracts\TelegramServiceInterface;

class SendTelegramMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Количество попыток
     */
    public int $tries = 3;

    /**
     * Интервал между попытками (секунды)
     */
    public array $backoff = [10, 60, 300];

    /**
     * Таймаут выполнения
     */
    public int $timeout = 30;

    public function __construct(
        private array $data = []
    ) {}

    public function handle(TelegramServiceInterface $telegram): void
    {
        // Если здесь Exception — Laravel сам:
        // 1. Ловит ошибку
        // 2. Ждёт backoff[0] = 10 сек
        // 3. Повторяет (попытка 2)
        // 4. Если опять ошибка — ждёт 60 сек
        // 5. Повторяет (попытка 3)
        // 6. Если опять ошибка — вызывает failed()

        $telegram->sendMessage($telegram->formatMessage($this->data));

        Log::info('Telegram message sent', [
            'data' => $this->data,
        ]);
    }

    /**
     * Все попытки провалились
     */
    public function failed(Throwable $exception): void
    {
        $telegram = app(TelegramServiceInterface::class);

        Log::error('Telegram send failed permanently', [
            'error'   => $exception->getMessage(),
            'data'    => $this->data,
            'message' => $telegram->formatMessage($this->data),
        ]);
    }
}
