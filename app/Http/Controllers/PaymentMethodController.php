<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return Inertia::render('Payment/Index', [
            'paymentMethods' => PaymentResource::collection(Payment::all())
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $path = $request->file('logo')->store('payment-logos', 'public');

        $payment = Payment::create([
            'name' => $request->name,
            'logo' => $path
        ]);

        // Remove created_at and updated_at from attributes for logging
        $attributes = $payment->toArray();
        unset($attributes['created_at'], $attributes['updated_at'], $attributes['id'],$attributes['logo']);

        // Log activity (creation)
        activity()
            ->causedBy(auth()->user())
            ->performedOn($payment)
            ->withProperties(['attributes' => $attributes])
            ->log('Created payment method: ' . $payment->name);

        return redirect()->back();
    }

    public function destroy(Payment $payment)
    {
        $paymentName = $payment->name;
        $paymentData = $payment->toArray();
        unset($paymentData['created_at'], $paymentData['updated_at']);

        if ($payment->logo) {
            Storage::disk('public')->delete($payment->logo);
        }
        
        $payment->delete();

        // Log activity (deletion)
        $attributes = $payment->toArray();
        unset($attributes['created_at'], $attributes['updated_at'], $attributes['id'],$attributes['logo']);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($payment)
            ->withProperties(['attributes' => $attributes])
            ->log('Deleted payment method: ' . $paymentName);

        return redirect()->back();
    }
}