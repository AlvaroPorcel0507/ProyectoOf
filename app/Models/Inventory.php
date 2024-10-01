<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

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
        return $this->belongsTo(Product::class, 'idProduct', 'id');
    }
}
