<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $table = 'user';
    protected $fillable = [
        'name',
        'lastName',
        'secondLastName',
        'role',
        'location',
        'status',
        'email',
        'password',
        'userId',
    ];

    public function sales()
    {
        return $this->hasMany(Sales::class, 'idUser', 'id');
    }

    public function invetories()
    {
        return $this->hasMany(Inventories::class, 'idUser', 'id');
    }

    public function activities()
    {
        return $this->hasMany(Activities::class, 'idUser', 'id');
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
}
