<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    // HasRoles = ini merupakan library spatie yang digunakan untuk manajemen roles dan permissions
    // dengan library ini kita dapat dengan mudah memberikan role dan permission kepada user
    // misalnya kita dapat memberikan role 'admin' kepada user dan memberikan permission 'edit articles' kepada role 'admin'
    // sehingga user yang memiliki role 'admin' dapat mengedit artikel
    // dst

    // HasApiTokens = ini merupakan trait dari laravel sanctum yang digunakan untuk mengelola token API
    // dengan trait ini kita dapat memberikan token API kepada user
    // sehingga user dapat mengakses API dengan token tersebut
    // dst

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'phone',
        'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookingTransactions()
    {
        return $this->hasMany(BookingTransaction::class);
    }

    public function getPhotoAttribute($value)
    {
        if (!$value) {
            return null;
        }

        return url(Storage::url($value));
    }
}
