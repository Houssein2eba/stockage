<?php

namespace App\Observers;

use App\Models\ProductStock;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Notification;

class ProductStockObserver
{
    /**
     * Handle the ProductStock "created" event.
     */
    public function created(ProductStock $productStock): void
    {
        //
    }

    /**
     * Handle the ProductStock "updated" event.
     */
    public function updated(ProductStock $productStock): void
    {

                $status = 'good';
                $users=User::get();
                $stockQuantity=$productStock->stock->products()->where('quantity', '>', '0')->count();

        if ($stockQuantity <= 0) {
            $status = 'empty';
        } elseif ($stockQuantity <= 10 && $stockQuantity > 0) {
            $status = 'low';
            if($stockQuantity == $productStock->stock->last_notified_quantity){
                return;
            }

            foreach($users as $user) {
                Notification::send($user, new LowStockNotification($productStock->stock()->first()));
            }

        }

        $productStock->stock()->update(['status' => $status,'last_notified_quantity'=>$stockQuantity]);

    }

    /**
     * Handle the ProductStock "deleted" event.
     */
    public function deleted(ProductStock $productStock): void
    {
        //
    }

    /**
     * Handle the ProductStock "restored" event.
     */
    public function restored(ProductStock $productStock): void
    {
        //
    }

    /**
     * Handle the ProductStock "force deleted" event.
     */
    public function forceDeleted(ProductStock $productStock): void
    {
        //
    }
}
