<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255'],
            'tg'       => ['nullable', 'string', 'max:100'],
            'message'  => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
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
