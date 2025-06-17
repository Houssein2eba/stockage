<?php

namespace App\Observers;

use App\Jobs\SendFcmOrderNotification;
use App\Models\Order;
use Auth;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $user=Auth::user();
        $message="Une vente vient d'etre crée par {$user->name} avec un montant de {$order->order_total_amount} MRU";
        // SendFcmOrderNotification::dispatch($message);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
