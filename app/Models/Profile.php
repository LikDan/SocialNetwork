<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $profile)
 */
class Profile extends Model
{
    use HasFactory;


    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, "from_profile_id");
    }

    public function subscribers()
    {
        return $this->hasMany(Subscription::class, "to_profile_id");
    }

    protected $fillable = [
        'nickname',
        'birthday',
        'is_private',
        'picture_path',
        'user_id',
    ];
}
