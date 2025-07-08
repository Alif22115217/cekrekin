<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'telepon',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart() {
        return $this->hasMany(Carts::class, 'user_id', 'id');
    }

    public function reservasi() {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function payment() {
        return $this->hasMany(Payment::class,'user_id','id');
    }
}
