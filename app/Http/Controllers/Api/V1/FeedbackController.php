<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FeedbackRequest;
use App\Jobs\SendTelegramMessage;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class FeedbackController extends Controller
{
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
