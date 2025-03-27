<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(){
        $activities = Activity::with(['causer', 'subject'])
                ->latest()
                ->paginate(5);
         
        return inertia('Activitys/Index',['activities'=>$activities]);
    }
    public function view($id){
        $activity=Activity::find($id);
        
        return inertia('Activitys/View',['activity'=>$activity]);
    }
}
