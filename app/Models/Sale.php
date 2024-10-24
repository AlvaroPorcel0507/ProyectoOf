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
        'producerId',
    ];
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'salesId');
    }

    // Definición de la relación con el cliente
    public function customer()
    {
        return $this->belongsTo(User::class, 'customerId', 'id');
    }

    // Definición de la relación con el vendedor
    public function producer()
    {
        return $this->belongsTo(User::class, 'producerId', 'id');
    }
}
