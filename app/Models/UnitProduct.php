<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'unitPrice',
        'measurementUnit',
        'status',
        'productId'
    ];

    public function product(){
        return $this->belongsTo(Products::class, 'productId', 'id');
    }
}
