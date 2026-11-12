<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FeedbackRequest;
use App\Jobs\SendTelegramMessage;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

/**
 * API-контроллер для отправки сообщений обратной связи v1.
 *
 * Принимает и валидирует данные формы обратной связи,
 * ставит задачу отправки сообщения в Telegram в очередь
 * и возвращает стандартизированный JSON-ответ.
 *
 * При наличии даты деприкации текущей версии API
 * добавляет предупреждение о необходимости перехода
 * на следующую версию.
 *
 * @package App\Http\Controllers\Api
 *
 * @see \App\Http\Requests\FeedbackRequest
 * @see \App\Jobs\SendTelegramMessage
 *
 * @throws \Illuminate\Contracts\Container\BindingResolutionException
 * @throws \Carbon\Exceptions\InvalidFormatException
 *
 * @phpstan-type FeedbackResponse array{
 *     success: bool,
 *     message: string,
 *     warning?: string
 * }
 */

class FeedbackController extends Controller
{
    /**
    * Отправка сообщения обратной связи.
    *
    * @param  \App\Http\Requests\FeedbackRequest  $request
    * @return \Illuminate\Http\JsonResponse
    *
    * @phpstan-return \Illuminate\Http\JsonResponse<FeedbackResponse>
    */

    public function send(FeedbackRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Отправляем в очередь
        SendTelegramMessage::dispatch($request->validated());

        $response = [
            'success' => true,
            'message' => 'Сообщение отправлено',
        ];

        $deprecationDate = config('api_version.deprecation_date');

        if ($deprecationDate && Carbon::parse($deprecationDate)->isFuture()) {
            $currentVersion = config('api_version.version');
            $nextVersion = 'v' . ((int) substr($currentVersion, 1) + 1);
            $response['warning'] = "API {$currentVersion} устарел. Перейдите на {$nextVersion} до {$deprecationDate}";
        }

        return response()->json($response);
    }
}
