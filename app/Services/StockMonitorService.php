<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;
use Log;

class StockMonitorService
{
    public function checkStockLevel($id)
    {
        $stock = Stock::find($id)->first();
        $settings = Setting::select('low_stock')->first();
        $lowStockThreshold = $settings->low_stock ?? 10;

        $stock->load('products'); // performance tip

        $totalQuantity = 0;
        foreach ($stock->products as $product) {
            $totalQuantity += $product->pivot->quantity;
        }

        $status = 'good';
        if ($totalQuantity == 0) {
            $status = 'empty';
        } elseif ($totalQuantity <= $lowStockThreshold) {
            $status = 'low';
        }

        // Always update status
        $stock->status = $status;

        // Notify only if quantity changed and it's not 'good'
        if ($status !== 'good' && $stock->last_notified_quantity != $totalQuantity) {
            $stock->last_notified_quantity = $totalQuantity;
            $stock->save();

            $message = "Stock' is low";

            $users = User::all();
            LowStockNotification::class::send($users, $message);
        } else {
            // Save status even if no notification
            $stock->save();
        }
    }
}
