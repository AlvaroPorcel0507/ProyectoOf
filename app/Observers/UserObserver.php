<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function creating(User $users){
        $users->userId = Auth::id();
    }
    public function updating(User $users){
        $users->userId = Auth::id();
    }
    public function destroying(User $users){
        $users->userId = Auth::id();
    }
}
