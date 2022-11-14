<?php

namespace App\Http\Requests\Api\v1;

use App\Models\PostType;
use Illuminate\Foundation\Http\FormRequest;

class PostTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "type" => "in:" . join(",", array_map(fn($el) => $el->value, PostType::cases())),
            "per_page" => 'integer|between:0,100',
            "from" => 'integer'
        ];
    }
}
