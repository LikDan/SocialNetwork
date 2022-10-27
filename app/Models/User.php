<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static create(mixed $validated)
 * @method static where(string $string, mixed $email)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function password(): Attribute
    {
        return new Attribute(
            set: fn ($value) => Hash::make($value),
        );
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];


    protected $hidden = [
        'password',
    ];
}
