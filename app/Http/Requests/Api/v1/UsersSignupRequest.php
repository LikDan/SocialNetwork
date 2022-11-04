<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class UsersSignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $date = now()->subYears(config("custom.min_age"));
        return [
            'user.name' => 'bail|required|max:255',
            'user.password' => 'bail|required|confirmed|min:8|max:255',
            'profile.nickname' => 'bail|required|max:255',
            'profile.is_private' => 'bail|bool',
            'profile.birthday' => 'bail|required|before:'.$date,
            'user.email' => 'required|email|unique:users,email|max:255',
        ];
    }
}
