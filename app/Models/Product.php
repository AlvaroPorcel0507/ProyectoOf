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
        'image',
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
}
