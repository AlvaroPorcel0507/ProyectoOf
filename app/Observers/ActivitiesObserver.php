<?php

namespace App\Observers;

use App\Models\Activities;
use Illuminate\Support\Facades\Auth;

class ActivitiesObserver
{
    public function creating(Activities $activities){
        $activities->userId = Auth::id();
    }
    public function updating(Activities $activities){
        $activities->userId = Auth::id();
    }
    public function destroying(Activities $activities){
        $activities->userId = Auth::id();
    }
}
