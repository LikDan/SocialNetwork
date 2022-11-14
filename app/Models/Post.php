<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\Post
 *
 * @method static create(mixed $validated)
 * @method static where(array $array)
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string|null $text
 * @property string $type
 * @property int $profile_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attachment[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Profile[] $likedProfiles
 * @property-read int|null $liked_profiles_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $ownerSubscribers
 * @property-read int|null $owner_subscribers_count
 * @property-read \App\Models\Profile $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|Post availablePosts()
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereProfileId($value)
 * @method static Builder|Post whereText($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereType($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @mixin \Eloquent
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


    public function ownerSubscribers(): HasMany
    {
        return $this
            ->hasMany(Subscription::class, "to_profile_id", "profile_id")
            ->where("status", SubscriptionStatus::Approved->value);
    }

    public function scopeAvailablePosts(Builder $query): Builder
    {
        $currentProfileId = Auth::user()->profile->id;
        return $query
            ->where(fn(Builder $query) => $query
                ->where('type', PostType::Published->value)
                ->orWhere('profile_id', $currentProfileId)
            )
            ->whereHas('profile', fn(Builder $query) => $query
                ->where("profile_id", $currentProfileId)
                ->orWhere('is_private', false)
                ->orWhereHas('subscribers', fn(Builder $query) => $query
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
