<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = ['password'];

    /**
     * 🔗 Relations
     */
    public function merchantSupplies()
    {
        return $this->hasMany(MerchantSupply::class, 'user_id');
    }

    public function ordersAsCouturier()
    {
        return $this->hasMany(Order::class, 'couturier_id');
    }

    public function ordersAsMercerie()
    {
        return $this->hasMany(Order::class, 'mercerie_id');
    }

    /**
     * ✅ Vérifications de rôle
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMercerie(): bool
    {
        return $this->role === 'mercerie';
    }

    public function isCouturier(): bool
    {
        return $this->role === 'couturier';
    }
}
