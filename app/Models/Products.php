<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products'; 

    protected $fillable = [
        'name',
        'description',
        'stock',
        'unitPrice',
        'status',
        'userId',
        'categoryId',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'categoryId', 'id');
    }

    public function salesDetail()
    {
        return $this->belongsTo(SalesDetail::class, 'productId', 'id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventories::class, 'idProduct', 'id');
    }
}
