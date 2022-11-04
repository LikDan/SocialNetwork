<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static create(mixed $validated)
 * @method static where(array $array)
 */
class Post extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function likedProfiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, "likes", "post_id", "profile_id");
    }

    public function likedCurrentProfiles(): BelongsToMany
    {
        return $this->likedProfiles()->where("profile_id", Auth::user()->profile->id);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, "profile_id");
    }

    public function scopeAvailablePosts(Builder $query): Builder
    {
        $currentProfileId = Auth::user()->profile->id;
        return $query
            ->where('type', PostType::Published->value)
            ->whereHas('profile', fn(Builder $query) => $query
                ->where("profile_id", $currentProfileId)
                ->orWhere('is_private', false)
                ->orWhereHas('subscriptions', fn(Builder $query) => $query
                    ->where('from_profile_id', $currentProfileId)
                    ->where('status', SubscriptionStatus::Approved->value)
                )
            );
    }

    protected $fillable = [
        'title',
        'text',
        'type',
        'profile_id',
    ];

}
