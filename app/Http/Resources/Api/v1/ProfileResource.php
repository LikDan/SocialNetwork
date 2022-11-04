<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'picture_path' => Storage::url($this->picture_path),
            'birthday' => $this->birthday,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
