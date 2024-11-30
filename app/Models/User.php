<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Ganti Model dengan Authenticatable
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Jika menggunakan Sanctum untuk API token

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    /**
     * Atribut yang disembunyikan untuk array.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data asli.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
