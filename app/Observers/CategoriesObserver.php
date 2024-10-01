<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoriesObserver
{
    public function creating(Category $categories){
        $categories->userId = Auth::id();
    }
    public function updating(Category $categories){
        $categories->userId = Auth::id();
    }
    public function destroying(Category $categories){
        $categories->userId = Auth::id();
    }
}
