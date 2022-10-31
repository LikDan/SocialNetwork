<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
enum SubscriptionStatus: string
{
    case Approved = "APPROVED";
    case Declined = "DECLINED";
    case Pending = "PENDING";
}
