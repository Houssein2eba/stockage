<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer');
        
        if ($request->has('dates')) {
            $date = Carbon::parse($request->dates);
            $query->whereDate('created_at', $date);
        }
  
        $activities = $query->paginate(5);

        return inertia('Activitys/Index', ['activities' => $activities]);
    }
    public function view($id){
        $activity=Activity::find($id);
        
        return inertia('Activitys/View',['activity'=>$activity]);
    }
}
