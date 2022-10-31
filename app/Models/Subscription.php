<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @method static create(array $subscription)
 */
class Subscription extends Model
{
    use HasFactory;

    public function fromProfile()
    {
        return $this->belongsTo(Profile::class, "from_profile_id");
    }

    public function toProfile()
    {
        return $this->belongsTo(Profile::class, "to_profile_id");
    }

    protected $fillable = [
        'from_profile_id',
        'to_profile_id',
        'status',
    ];
}
