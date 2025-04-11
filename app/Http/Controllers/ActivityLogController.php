<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        
        $activities=Activity::with(['causer','subject'])->when($request->dates, function ($query) use ($request) {
            $date = Carbon::parse($request->dates);
            $query->whereDate('created_at', $date);
        })->latest()->paginate(PAGINATION)->withQueryString();
        return inertia('Activitys/Index', ['activities' => $activities,'dates'=>$request->dates]);
    }

    public function view($id){
        $activity=Activity::find($id);
        
        return inertia('Activitys/View',['activity'=>$activity]);
    }
}
