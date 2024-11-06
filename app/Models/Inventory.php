<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'measurementUnit',
        'unitPrice',
        'productId',
        'userId',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }

}
