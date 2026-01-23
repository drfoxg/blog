<?php

namespace App\Providers;

use App\Http\Contracts\TelegramServiceInterface;
use App\Services\Telegram\V1\TelegramService as TelegramV1;
use App\Services\Telegram\V2\TelegramService as TelegramV2;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TelegramServiceInterface::class, function () {
            return match (config('api_version.version', 'v1')) {
                'v2'    => new TelegramV2(),
                default => new TelegramV1(),
            };
        });
    }
}
