<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'scheduledDate',
        'duration',
        'priority',
        'status',
        'idUser',
        'userId',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'idUser', 'id');
    }
}
