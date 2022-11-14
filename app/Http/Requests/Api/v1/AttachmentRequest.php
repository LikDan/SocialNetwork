<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "file" => 'required|file|mimetypes:image/gif,audio/mpeg,audio/mpeg,video/mpeg,image/png,image/jpeg,application/x-mpegURL,video/MP2T',
            //https://stackoverflow.com/questions/22378742/laravel-video-file-validation
        ];
    }
}
