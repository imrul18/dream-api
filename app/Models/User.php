<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'city',
        'state',
        'postal_address',
        'postal_code',
        'country',
        'govt_id',
        'username',
        'profile_image',
        'email',
        'password',
        'isAdmin',
        'status'
    ];

    protected $appends = [
        'current_status', 'profile_image_url'
    ];

    public function getCurrentStatusAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function getProfileImageUrlAttribute()
    {
        return asset($this->profile_image);
    }

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
}
