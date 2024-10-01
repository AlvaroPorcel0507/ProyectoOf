<?php

namespace App\Observers;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivitiesObserver
{
    public function creating(Activity $activities){
        $activities->userId = Auth::id();
    }
    public function updating(Activity $activities){
        $activities->userId = Auth::id();
    }
    public function destroying(Activity $activities){
        $activities->userId = Auth::id();
    }
}
