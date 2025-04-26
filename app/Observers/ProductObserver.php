<?php

namespace App\Observers;

use App\Models\Product;
use App\Notifications\ProductNotification;
use Illuminate\Support\Facades\Notification;

class ProductObserver
{
    public function created(Product $product): void
    {
        // Create a simple notifiable object
        $notifiable = new class {
            public function routeNotificationForTwilio() {
                return config('services.twilio.admin_number');
            }
        };

        Notification::send($notifiable, new ProductNotification($product));
    }


    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
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
