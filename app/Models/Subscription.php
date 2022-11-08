<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * App\Models\Subscription
 *
 * @method static create(array $subscription)
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $from_profile_id
 * @property int $to_profile_id
 * @property string $status
 * @property-read \App\Models\Profile $fromProfile
 * @property-read \App\Models\Profile $toProfile
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereFromProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereToProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    use HasFactory;

    public function fromProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, "from_profile_id");
    }

    public function toProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, "to_profile_id");
    }

    protected $fillable = [
        'from_profile_id',
        'to_profile_id',
        'status',
    ];
}
