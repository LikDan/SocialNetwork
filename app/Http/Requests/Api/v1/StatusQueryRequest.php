<?php

namespace App\Http\Requests\Api\v1;

use App\Models\SubscriptionStatus;
use Illuminate\Foundation\Http\FormRequest;

class StatusQueryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "status" => "in:".SubscriptionStatus::Approved->value.",".SubscriptionStatus::Pending->value.",".SubscriptionStatus::Declined->value
        ];
    }
}
