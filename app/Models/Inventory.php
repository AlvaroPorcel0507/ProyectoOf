<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'productId',
        'userId',
    ];
    public function user()
    {
        return $this->belongsTo(Inventory::class, 'userId', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }
}
