<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
{
    $last_notified_quantity = $product->last_notified_quantity;
    $previous_quantity = $product->getOriginal('quantity');
    $current_quantity = $product->quantity;
    $low_threshold = 10;

    if ($current_quantity <= $low_threshold && $previous_quantity != $last_notified_quantity) {
        $message = "Product {$product->name} is low on stock. Current quantity: {$current_quantity}";

        // Optimize by sending to all users at once
        $users = User::select('id')->get(); // Only get IDs
        Notification::send($users, new \App\Notifications\LowProductNotification($message));

        // Update the last notified quantity without triggering another update
        $product->withoutEvents(function () use ($product, $current_quantity) {
            $product->update(['last_notified_quantity' => $current_quantity]);
        });
    }
}

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
