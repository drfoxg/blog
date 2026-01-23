<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\FeedbackRequest;
use App\Jobs\SendTelegramMessage;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
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
