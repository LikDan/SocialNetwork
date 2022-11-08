<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePicturePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUserId($value)
 * @mixin \Eloquent
 */
class Profile extends Model
{
    use HasFactory;

    public function subscribedProfiles(): BelongsToMany {
        return $this->belongsToMany(Profile::class, "subscriptions", "from_profile_id", "to_profile_id")
            ->wherePivot("status", SubscriptionStatus::Approved->value);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, "to_profile_id");
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscription::class, "from_profile_id");
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
    ];

    protected $hidden = [
        'user_id',
    ];
}
