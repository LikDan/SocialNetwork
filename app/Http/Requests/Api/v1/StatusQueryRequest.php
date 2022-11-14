<?php

namespace App\Http\Requests\Api\v1;

use App\Models\PostType;
use App\Models\SubscriptionStatus;
use Illuminate\Foundation\Http\FormRequest;

class StatusQueryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "status" => "in:".join(",", array_map(fn($el) => $el->value, SubscriptionStatus::cases()))
        ];
    }
}
