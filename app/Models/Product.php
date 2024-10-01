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

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'idProduct', 'id');
    }
    public function unitProduct()
    {
        return $this->hasMany(UnitProduct::class, 'productId', 'id');
    }



    public function getUnitProducts()
    {
        return $this->unitProduct()->get();
    }
}
