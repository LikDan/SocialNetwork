<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class AvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "avatar" => "required|file|mimetypes:image/jpeg,image/png,image/gif,application/x-mpegURL"
            //https://stackoverflow.com/questions/22378742/laravel-video-file-validation
        ];
    }
}
