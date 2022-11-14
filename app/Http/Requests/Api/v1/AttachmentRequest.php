<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "file" => 'required|file|mimetypes:image/gif,audio/mpeg,audio/mpeg,video/mpeg,image/png,image/jpeg',
        ];
    }
}
