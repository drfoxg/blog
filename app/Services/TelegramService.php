<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class TelegramService
{
    private string $token;
    private string $chatId;
    private string $apiUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Отправить сообщение в Telegram
     *
     * @throws RequestException
     */
    public function sendMessage(string $text, ?string $chatId = null): bool
    {
        $response = Http::timeout(10)
            ->retry(3, 100)
            ->post("{$this->apiUrl}/sendMessage", [
                'chat_id'    => $chatId ?? $this->chatId,
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json('ok', false);
    }

    /**
     * Проверить работоспособность бота
     */
    public function ping(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->apiUrl}/getMe");

            return $response->successful() && $response->json('ok', false);
        } catch (\Exception) {
            return false;
        }
    }
}
