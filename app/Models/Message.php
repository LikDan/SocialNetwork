<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use HasFactory;

    public function senderProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, "from_profile_id");
    }

    protected $fillable = [
        'message',
        'to_profile_id',
        'from_profile_id',
    ];
}
