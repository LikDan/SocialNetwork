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

    protected $fillable = [
        'nickname',
        'birthday',
        'is_private',
        'picture_path',
        'user_id',
    ];
}
