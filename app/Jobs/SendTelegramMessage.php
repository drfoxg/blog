<?php

namespace App\Jobs;

use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        private string $message,
        private array $data = []
    ) {}

    public function handle(TelegramService $telegram): void
    {
        // Если здесь Exception — Laravel сам:
        // 1. Ловит ошибку
        // 2. Ждёт backoff[0] = 10 сек
        // 3. Повторяет (попытка 2)
        // 4. Если опять ошибка — ждёт 60 сек
        // 5. Повторяет (попытка 3)
        // 6. Если опять ошибка — вызывает failed()

        $telegram->sendMessage($this->message);

        Log::info('Telegram message sent', [
            'data' => $this->data,
        ]);
    }

    /**
     * Все попытки провалились
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Telegram send failed permanently', [
            'error'   => $exception->getMessage(),
            'data'    => $this->data,
            'message' => $this->message,
        ]);
    }
}
