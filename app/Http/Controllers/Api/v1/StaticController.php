<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionStatus;
use Illuminate\Http\JsonResponse;

class StaticController extends Controller
{
    public function static(): JsonResponse
    {
        $cases = SubscriptionStatus::cases();
        return response()->json([
            "subscriptions" => [
                "statuses" => collect($cases)->map(fn($a) => ["name" => $a->name, "value" => $a->value]),
            ]
        ]);
    }
}
