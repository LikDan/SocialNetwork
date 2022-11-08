<?php

namespace App\Http\Requests\Api\v1;

use App\Models\PostType;
use Illuminate\Foundation\Http\FormRequest;

class PostCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "title" => "required|min:10|max:255",
            "text" => "nullable|string",
            "type" => "required|in:".join(",", array_map(fn($el) => $el->value, PostType::cases()))
        ];
    }
}
