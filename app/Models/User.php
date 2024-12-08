<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Restaurant;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    private const ALL_ROLES = [
        'admin',
        'manager',
        'customer',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if (!in_array($this->role, self::ALL_ROLES)) {
            Auth::logout();            
            session()->flash('error', 'Unknown role');
            return false;
        }
    
        return match ($panel->getId()) {
            'admin' => $this->role === 'admin',
            'manager' => $this->role === 'manager',
            'customer' => $this->role === 'customer',
            default => false
        };
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'manager_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function loyaltyPoint()
    {
        return $this->hasOne(LoyaltyPoint::class, 'customer_id');
    }

}