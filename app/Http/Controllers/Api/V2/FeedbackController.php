<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\FeedbackRequest;
use App\Jobs\SendTelegramMessage;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Container\BindingResolutionException;
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
 * @see \App\Http\Requests\FeedbackRequest
 * @see SendTelegramMessage
 *
 * @throws BindingResolutionException
 * @throws InvalidFormatException
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
     * @param \App\Http\Requests\FeedbackRequest $request
     *
     * @phpstan-return JsonResponse<FeedbackResponse>
     */
    public function send(FeedbackRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Отправляем в очередь
        SendTelegramMessage::dispatch($data);

        return response()->json([
            'success' => true,
            'message' => 'Сообщение отправлено',
        ]);
    }
}
