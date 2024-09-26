<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales'; 

    protected $fillable = [
        'status',
        'total',
        'userId',
        'idUser',
        'customerId',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customerId', 'id');
    }

    public function salesDetail()
    {
        return $this->hasMany(SalesDetail::class, 'salesId', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'idUser', 'id');
    }
}
