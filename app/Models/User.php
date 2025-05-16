<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_users';
    protected $table = 'users';

    protected $fillable = [
        'name',
        'phone',
        'password',
        'role',
        'is_subscribed',
        'active_until'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'active_until' => 'datetime',
        'is_subscribed' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_users');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'id_users');
    }
}
