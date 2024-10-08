<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'userId',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'categoryId', 'id');
    }
}
