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
        'status',
        'userId',
        'categoryId',
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'categoryId', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productsId');
    }

    public function users()
    {
        return $this->belongsTo(Product::class, 'userId', 'id');
    }

    public function totalProduct()
    {
        return $this->hasMany(TotalProduct::class, 'productId', 'id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'productId', 'id');
    }
}
