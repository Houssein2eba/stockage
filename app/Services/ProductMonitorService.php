<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\LowProductNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Log;

class ProductMonitorService
{

    public function __construct()
    {

    }

    public function checkProductLevel($productId, $stockId)
    {
        $lowProductThreshold = Setting::value('low_product') ?? 5;

        $product = Product::with(['stocks' => function ($query) use ($stockId) {
            $query->where('stock_id', $stockId);
        }])->find($productId);

        // Check if product exists and is attached to the specified stock
        if (!$product || $product->stocks->isEmpty()) {
            return;
        }

        $stock = $product->stocks->first();
        $quantity = $stock->pivot->quantity;
        $lastNotifiedQuantity = $stock->pivot->last_notified_quantity ?? null;
        $expiryDate = isset($stock->pivot->expiry_date) ? Carbon::parse($stock->pivot->expiry_date) : null;

        $users = User::all(); // You may filter only admin users here

        // Low stock notification
        if ($quantity <= $lowProductThreshold && $quantity != $lastNotifiedQuantity) {
            $message = "⚠️ Warning: Product '{$product->name}' is low in stock at '{$stock->name}'. Current quantity: {$quantity}.";
            Notification::send($users, new LowProductNotification($message));

            // Update the pivot with last notified quantity
            $product->stocks()->updateExistingPivot($stockId, [
                'last_notified_quantity' => $quantity,
            ]);
        }

        // Expired or about to expire notification
        if ($expiryDate) {
            if ($expiryDate->isPast()) {
                $message = "❌ Danger: Product '{$product->name}' in stock '{$stock->name}' has expired. Current quantity: {$quantity}.";
                Notification::send($users, new LowProductNotification($message));
            } elseif ($expiryDate->isBetween(now(), now()->addWeek())) {
                $message = "⏰ Warning: Product '{$product->name}' in stock '{$stock->name}' is about to expire. Expiry: {$expiryDate->toDateString()}.";
                Notification::send($users, new LowProductNotification($message));
            }
        }
    }

}
