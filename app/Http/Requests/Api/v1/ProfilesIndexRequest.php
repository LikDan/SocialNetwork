<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class ProfilesIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "per_page" => 'integer|between:10,100'
        ];
    }
}
