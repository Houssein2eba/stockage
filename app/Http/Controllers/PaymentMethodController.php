<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
         // Number of items per page
        $request->validate([
            'search' => 'nullable|string',
            'sort' => 'nullable|string|in:name,created_at,updated_at',
            'direction' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ]);



        $paymentMethods = Payment::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->sort && $request->direction, function ($query) use ($request) {
                $query->orderBy($request->sort, $request->direction);
            })
            ->latest()
            ->paginate(PAGINATION)
            ->withQueryString();

        return Inertia::render('Payment/Index', [
            'paymentMethods' => PaymentResource::collection($paymentMethods),
            'filters' => $request->only(['search', 'sort', 'direction']),
            'payment_count' => DB::table('payments')->count(),
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
