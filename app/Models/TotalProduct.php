<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'productId',
        'stock',
    ];
    // Relación con la venta
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }
}
