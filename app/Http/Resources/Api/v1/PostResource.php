<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'title' => $this->title,
            'text' => $this->text,
            'type' => $this->type,
            'profile' => ShortProfileResource::make($this->whenLoaded('owner')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'likes_amount' => $this->whenCounted('liked_profiles_count'),
            'is_liked' => (bool) $this->whenCounted('liked_current_profiles_count'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
