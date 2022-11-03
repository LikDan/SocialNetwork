<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
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

    protected $fillable = [
        'title',
        'text',
        'type',
        'profile_id',
    ];

}
