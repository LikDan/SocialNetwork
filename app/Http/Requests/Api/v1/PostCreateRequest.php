<?php

namespace App\Http\Requests\Api\v1;

use App\Models\PostType;
use Illuminate\Foundation\Http\FormRequest;

class PostCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title" => "required|min:10|max:255",
            "text" => "nullable|string",
            "type" => "required|in:".join(",", array_map(fn($el) => $el->value, PostType::cases()))
        ];
    }
}
