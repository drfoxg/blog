<?php

namespace App\Http\Requests\Concerns;

trait FeedbackRules
{
    protected function feedbackRules(): array
    {
        return [
            'username' => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255'],
            'tg'       => ['nullable', 'string', 'max:100'],
            'message'  => ['nullable', 'string', 'max:2000'],
            // Антиспам-поля — просто пропускаем, не валидируем
            'company'         => ['nullable'],
            'form_started_at' => ['nullable'],
        ];
    }

    protected function feedbackMessages(): array
    {
        return [
            'username.required' => 'Укажите ваше имя',
            'username.max'      => 'Имя слишком длинное',
            'email.required'    => 'Укажите email',
            'email.email'       => 'Некорректный email',
            'message.max'       => 'Сообщение слишком длинное',
        ];
    }
}
