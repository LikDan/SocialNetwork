<?php

namespace App\Http\Requests\Api\v1;

use App\Models\PostType;
use Illuminate\Foundation\Http\FormRequest;

class PostTypeRequest extends FormRequest
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
            "type" => "in:".join(",", array_map(fn($el) => $el->value, PostType::cases()))
        ];
    }
}
