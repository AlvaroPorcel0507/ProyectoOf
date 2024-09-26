<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    use HasFactory;

    protected $table = 'salesDetail';

    protected $fillable = [
        'quantity',
        'unitPrice',
        'totalProduct',
        'salesId',
        'productsId'
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'salesId', 'id');
    }

    public function products()
    {
        return $this->belongsTo(Products::class, 'productsId', 'id');
    }
}
