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
            'user.name' => 'required',
            'user.password' => 'required|confirmed|min:8',
            'user.email' => 'required|email|unique:users,email',
            'profile.nickname' => 'required',
            'profile.is_private' => 'bool',
            'profile.birthday' => 'required|before:'.$date,
        ];
    }
}
