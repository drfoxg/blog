<?php

namespace App\Services\Telegram;

use App\Http\Contracts\TelegramServiceInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

abstract class AbstractTelegramService implements TelegramServiceInterface
{
    protected string $token;
    protected string $chatId;
    protected string $apiUrl;
    protected string $proxyUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
        $this->proxyUrl = config('services.telegram.proxy_url');
    }

    public function ping(): bool
    {
        try {
            $response = $this->http()->get("{$this->apiUrl}/getMe");

            return $response->successful() && $response->json('ok', false);
        } catch (\Exception) {
            return false;
        }
    }

    abstract public function sendMessage(string $text, ?string $chatId = null): bool;

    abstract public function getVersion(): string;

    /**
     * HTTP-клиент для запросов к Telegram Bot API.
     *
     * - proxy: обход блокировки api.telegram.org (tgwsapiproxy)
     * - force_ip_resolve: v4 — в окружении нет рабочего IPv6,
     *   без этого cURL тратит время на попытки IPv6 и падает по таймауту.
     */
    protected function http(): PendingRequest
    {
        $options = ['force_ip_resolve' => 'v4'];

        if ($proxy = config('services.telegram.proxy_url')) {
            $options['proxy'] = $proxy;
        }

        return Http::withOptions($options)->timeout(10);
    }
}
