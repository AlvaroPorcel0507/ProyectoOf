<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'stock',
        'unitPrice',
        'measurementUnit',
        'status',
        'userId',
        'categoryId',
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'categoryId', 'id');
    }

    public function saleDetails()
    {
        return $this->belongsTo(SaleDetail::class, 'productId', 'id');
    }

    public function users()
    {
        return $this->belongsTo(Product::class, 'userId', 'id');
    }
}
