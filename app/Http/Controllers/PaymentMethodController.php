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

        Payment::create([
            'name' => $request->name,
            'logo' => $path
        ]);

        return redirect()->back();
    }

    public function destroy(Payment $payment)
    {
        if ($payment->logo) {
            Storage::disk('public')->delete($payment->logo);
        }
        
        $payment->delete();

        return redirect()->back();
    }
}