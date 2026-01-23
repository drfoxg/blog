<?php

namespace App\Http\Requests\V1;

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
        return $this->feedbackRules();
    }

    public function messages(): array
    {
        return $this->feedbackMessages();
    }
}
