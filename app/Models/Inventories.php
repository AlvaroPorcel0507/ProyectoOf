<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventories extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'quantity',
        'userId',
        'idUser',
        'idProduct',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'idUser', 'id');
    }

    public function products()
    {
        return $this->belongsTo(Products::class, 'idProduct', 'id');
    }
}
