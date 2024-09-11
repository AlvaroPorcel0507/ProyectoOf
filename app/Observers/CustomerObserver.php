<?php

namespace App\Observers;

use App\Models\Customers;
use Illuminate\Support\Facades\Auth;

class CustomerObserver
{
    public function creating(Customers $customer){
        $customer->userId = Auth::id();
    }
    public function updating(Customers $customer){
        $customer->userId = Auth::id();
    }
    public function destroying(Customers $customer){
        $customer->userId = Auth::id();
    }
}
