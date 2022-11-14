<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class MessagesIndexRequest extends FormRequest
{
    public function rules()
    {
        return [
            "per_page" => 'integer|between:0,100'
        ];
    }
}
