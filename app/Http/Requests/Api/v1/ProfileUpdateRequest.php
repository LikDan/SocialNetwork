<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $date = now()->subYears(config("custom.min_age"));
        return [
            'nickname' => 'required|max:255',
            'is_private' => 'bool',
            'birthday' => 'required|before:'.$date,
        ];
    }
}
