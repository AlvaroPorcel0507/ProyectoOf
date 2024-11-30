<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    
    protected $fillable = [
        'salesId',
        'productsId',
        'producerId',
        'description',
        'quantity',
        'unitPrice',
        'totalProduct',
    ];
    // Relación con la venta
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'salesId');
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Product::class, 'productsId');
    }
    public function producer()
    {
        return $this->belongsTo(User::class, 'producerId');
    }
}
