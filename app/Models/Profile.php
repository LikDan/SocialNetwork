<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\Profile
 *
 * @method static create(mixed $profile)
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $nickname
 * @property string $picture_path
 * @property int $is_private
 * @property string $birthday
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Profile[] $subscribedProfiles
 * @property-read int|null $subscribed_profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscribers
 * @property-read int|null $subscribers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static Builder|Profile newModelQuery()
 * @method static Builder|Profile newQuery()
 * @method static Builder|Profile query()
 * @method static Builder|Profile whereBirthday($value)
 * @method static Builder|Profile whereCreatedAt($value)
 * @method static Builder|Profile whereId($value)
 * @method static Builder|Profile whereIsPrivate($value)
 * @method static Builder|Profile whereNickname($value)
 * @method static Builder|Profile wherePicturePath($value)
 * @method static Builder|Profile whereUpdatedAt($value)
 * @method static Builder|Profile whereUserId($value)
 * @mixin \Eloquent
 */
class Profile extends Model
{
    use HasFactory, Notifiable;

    public function subscribedProfiles(): BelongsToMany {
        return $this->belongsToMany(Profile::class, "subscriptions", "from_profile_id", "to_profile_id")
            ->wherePivot("status", SubscriptionStatus::Approved->value);
    }

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

    public function messagesToMe(): HasMany
    {
        return $this->hasMany(Message::class, "to_profile_id");
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'profiles.'.$this->id;
    }


    protected $fillable = [
        'nickname',
        'birthday',
        'is_private',
        'picture_path',
    ];

    protected $hidden = [
        'user_id',
    ];
}
