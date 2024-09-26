<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    
    protected $fillable = [
        'salesId',
        'productsId',
        'quantity',
        'unitPrice',
        'total',
    ];
    // Relación con la venta
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'salesId');
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Products::class, 'productsId');
    }
}
