<?php

namespace App\Services\Telegram\V2;

use App\Services\Telegram\AbstractTelegramService;
use Illuminate\Support\Facades\Http;

class TelegramService extends AbstractTelegramService
{
    public function getVersion(): string
    {
        return 'v2';
    }

    public function sendMessage(string $text, ?string $chatId = null): bool
    {
        $replyMarkup = [
            'inline_keyboard' => [[
                ['text' => '✅ Сохранить', 'callback_data' => 'save'],
                ['text' => '❌ Удалить', 'callback_data' => 'del'],
            ]]
        ];

        return $this->sendMessageAdvanced($text, $chatId, $replyMarkup);
    }

    public function sendMessageAdvanced(
        string $text,
        ?string $chatId = null,
        ?array $replyMarkup = null,
        bool $disableNotification = false
    ): bool {
        $payload = [
            'chat_id'              => $chatId ?? $this->chatId,
            'text'                 => $text,
            'parse_mode'           => 'HTML',
            'disable_notification' => $disableNotification,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        $response = Http::timeout(10)
            ->retry(3, 100)
            ->post("{$this->apiUrl}/sendMessage", $payload);

        return $response->successful() && $response->json('ok', false);
    }

    public function formatMessage(array $data): string
    {
        $lines = [
            '📩 <b>Новая заявка с сайта</b>',
            '',
            '👤 <b>Имя:</b> ' . e($data['username']),
            '📧 <b>Email:</b> ' . e($data['email']),
            '📱 <b>Телефон:</b> ' . e($data['phone']),
        ];

        if (!empty($data['tg'])) {
            $lines[] = '💬 <b>Telegram:</b> ' . e($data['tg']);
        }

        if (!empty($data['message'])) {
            $lines[] = '';
            $lines[] = '📝 <b>Сообщение:</b>';
            $lines[] = e($data['message']);
        }

        return implode("\n", $lines);
    }
}
