<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\FeedbackRequest;
use App\Jobs\SendTelegramMessage;
use Illuminate\Http\JsonResponse;

/**
 * API-контроллер для отправки сообщений обратной связи v2.
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
        SendTelegramMessage::dispatch($data);

        return response()->json([
            'success' => true,
            'message' => 'Сообщение отправлено'
        ]);
    }
}
