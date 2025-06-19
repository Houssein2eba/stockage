<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Log;

class SettingController extends Controller
{
    public function index(Request $request){
        $settings = Setting::get()->first();
        if($request->wantsJson()){
            return response()->json([
                'setting' => new SettingResource($settings),
            ]);
        }

        return Inertia::render('Setting/Index', [
            'settings' => new SettingResource($settings),
        ]);
    }



public function update(Request $request)
{
    Log::info($request->all());
    $validated = $request->validate([
        'company_name' => 'required|string',
        'address' => 'required|string',
        'phone' => 'required|regex:/^([2-4][0-9]{7})$/',
        'email' => 'required|email|string',
    ],[
        'phone.regex' => 'Le numéro doit commencer par 2, 3 ou 4 suivi de 7 chiffres',
        'email.email' => 'L\'email doit être une adresse email valide',
        'company_name.required' => 'Le nom de la société est obligatoire',
        'address.required' => 'L\'adresse est obligatoire',
        'email.required' => 'L\'email est obligatoire',
        'phone.required' => 'Le numéro est obligatoire',
    ]);

    $settings = Setting::first();
    if ($settings) {
        $settings->update($validated);
    } else {
        Setting::create($validated);
    }
    if($request->wantsJson()){
        return response()->json(['message' => 'Settings updated successfully']);
    }


    return redirect()->back()->with('success', 'Settings updated successfully');
}

}
