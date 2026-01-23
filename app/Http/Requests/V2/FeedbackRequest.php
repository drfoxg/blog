<?php

namespace App\Http\Requests\V2;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Concerns\FeedbackRules;

class FeedbackRequest extends FormRequest
{
    use FeedbackRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(
            $this->feedbackRules(),
            ['phone' => ['required', 'string', 'min:7', 'max:15', 'regex:/^([0-9\s\-\+\(\)]*)$/']],
        );
    }

    public function messages(): array
    {
        return array_merge(
            $this->feedbackMessages(),
            [
                'phone.required'    => 'Укажите телефон',
                'phone.min'         => 'Телефон слишком короткий',
                'phone.regex'       => 'Некорректный формат телефона',
            ],
        );
    }
}
