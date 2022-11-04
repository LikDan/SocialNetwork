<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'status' => $this->status,
            'to_profile_id' => $this->to_profile_id,
            'from_profile_id' => $this->from_profile_id,
            'to_profile' => ProfileResource::make($this->whenLoaded('toProfile')),
            'from_profile' => ProfileResource::make($this->whenLoaded('fromProfile')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
