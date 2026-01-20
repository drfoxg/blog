<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function __construct(
        private readonly TelegramService $telegram
    ) {}

    public function send(FeedbackRequest $request): JsonResponse
    {
        $data = $request->validated();

        $message = $this->formatMessage($data);

        try {
            $this->telegram->sendMessage($message);

            return response()->json([
                'success' => true,
                'message' => 'Сообщение отправлено'
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram send error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка отправки. Попробуйте позже.'
            ], 500);
        }
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
