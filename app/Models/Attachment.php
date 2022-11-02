<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use HasFactory;

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    protected $fillable = [
        'path',
        'display_name',
        'type',
    ];
}
