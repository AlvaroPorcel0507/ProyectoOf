<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    public function creating(Product $products){
        $products->userId = Auth::id();
    }
    public function updating(Product $products){
        $products->userId = Auth::id();
    }
    public function destroying(Product $products){
        $products->userId = Auth::id();
    }
}
