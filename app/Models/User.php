<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;


class User extends Authenticatable
{
    use HasFactory, Notifiable, Searchable;

    protected $guarded = [];

    public function getFormalNameAttribute()
    {
        return Str::title($this->first_name) . ' ' . Str::title($this->last_name);
    }

    public function vehicles()
    {
        return $this->hasMany(CustomerVehicle::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }


    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function gpsDevice()
    {
        return $this->hasOne(GpsDevice::class, 'assigned_to', 'id');
    }
}
