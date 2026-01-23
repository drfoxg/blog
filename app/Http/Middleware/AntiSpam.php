<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AntiSpam
{
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Honeypot
         * Поле должно существовать в форме, но быть пустым
         */
        if ($request->filled('company')) {
            return $this->fakeSuccess();
        }

        /**
         * TODO: Проверка времени заполнения формы
         * form_started_at — timestamp, который кладём в форму
         */
        // $startedAt = (int) $request->input('form_started_at', 0);

        // if ($startedAt > 0) {
        //     $fillTime = time() - $startedAt;

        //     // меньше 2 секунд — почти всегда бот
        //     if ($fillTime < 2) {
        //         return $this->fakeSuccess();
        //     }
        // }

        /**
         * Примитивная эвристика текста
         */
        $text = (string) $request->input('message', '');

        if ($this->looksLikeSpam($text)) {
            return $this->fakeSuccess();
        }

        return $next($request);
    }

    private function fakeSuccess(): Response
    {
        // ВАЖНО: всегда 200, чтобы бот не понял, что его отфильтровали
        return response()->json([
            'success' => true,
            'message' => 'Сообщение отправлено',
        ]);

    }

    private function looksLikeSpam(string $text): bool
    {
        if ($text === '') {
            return false;
        }

        // слишком длинный текст
        if (mb_strlen($text) > 2000) {
            return true;
        }

        // повторяющиеся слова
        if (preg_match('/(\b\w+\b)(?:\s+\1){3,}/iu', $text)) {
            return true;
        }

        // ссылки
        if (preg_match('/https?:\/\/|www\./i', $text)) {
            return true;
        }

        return false;
    }
}
