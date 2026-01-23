<?php

namespace App\Services\Telegram;

use App\Http\Contracts\TelegramServiceInterface;
use Illuminate\Support\Facades\Http;

abstract class AbstractTelegramService implements TelegramServiceInterface
{
    protected string $token;
    protected string $chatId;
    protected string $apiUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    public function ping(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/getMe");

            return $response->successful() && $response->json('ok', false);
        } catch (\Exception) {
            return false;
        }
    }

    abstract public function sendMessage(string $text, ?string $chatId = null): bool;

    abstract public function getVersion(): string;
}
