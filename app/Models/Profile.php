<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static create(mixed $profile)
 */
class Profile extends Model
{
    use HasFactory;


    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, "from_profile_id");
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscription::class, "to_profile_id");
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    protected $fillable = [
        'nickname',
        'birthday',
        'is_private',
        'picture_path',
        'user_id',
    ];
}
