<?php

namespace App\Observers;

use App\Models\Categories;
use Illuminate\Support\Facades\Auth;

class CategoriesObserver
{
    public function creating(Categories $categories){
        $categories->userId = Auth::id();
    }
    public function updating(Categories $categories){
        $categories->userId = Auth::id();
    }
    public function destroying(Categories $categories){
        $categories->userId = Auth::id();
    }
}
