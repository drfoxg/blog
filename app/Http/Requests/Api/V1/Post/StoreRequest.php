<?php

namespace App\Http\Requests\Api\V1\Post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'content' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'category_id' => 'sometimes|integer:exists:categories,id',
            'description_meta' => 'nullable|string',
            'description' => 'nullable|string',
            'published_at' => 'nullable|date_format:Y-m-d',
        ];
    }
}
