<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Jobs\SendTelegramMessage;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function send(FeedbackRequest $request): JsonResponse
    {
        $data = $request->validated();
        $message = $this->formatMessage($data);

        // Отправляем в очередь
        SendTelegramMessage::dispatch($message, $data);

        return response()->json([
            'success' => true,
            'message' => 'Сообщение отправлено'
        ]);
    }

    private function formatMessage(array $data): string
    {
        $lines = [
            '📩 <b>Новая заявка с сайта</b>',
            '',
            '👤 <b>Имя:</b> ' . e($data['username']),
            '📧 <b>Email:</b> ' . e($data['email']),
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
