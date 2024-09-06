<?php

namespace App\Observers;

use App\Models\Products;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    public function creating(Products $products){
        $products->userId = Auth::id();
    }
    public function updating(Products $products){
        $products->userId = Auth::id();
    }
    public function destroying(Products $products){
        $products->userId = Auth::id();
    }
}
