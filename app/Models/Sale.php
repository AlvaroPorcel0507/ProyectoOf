<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'total',
        'userId',
        'idUser',
        'customerId',
    ];
    public function customer()
    {
        return $this->belongsTo(User::class, 'customerId');
    }

    // Relación con los detalles de venta
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'salesId');
    }
}
