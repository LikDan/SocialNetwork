<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class UsersSignupRequest extends FormRequest
{
    public function rules(): array
    {
        $date = now()->subYears(config("custom.min_age"));
        return [
            'user.name' => 'required|max:255',
            'user.password' => 'required|confirmed|min:8|max:255',
            'profile.nickname' => 'required|max:255',
            'profile.is_private' => 'bool',
            'profile.birthday' => 'required|before:'.$date,
            'user.email' => 'required|email|unique:users,email|max:255',
        ];
    }
}
