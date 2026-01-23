<?php

namespace App\Http\Contracts;

interface TelegramServiceInterface
{
    /**
     * Отправить сообщение
     */
    public function sendMessage(string $text, ?string $chatId = null): bool;

    /**
     * Проверить доступность API
     */
    public function ping(): bool;

    /**
     * Получить версию API
     */
    public function getVersion(): string;

    public function formatMessage(array $data): string;
}
