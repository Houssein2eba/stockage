<?php

namespace App\Observers;

use App\Jobs\Orders\SendFcmOrderCreatedNotification;
use App\Jobs\SendFcmOrderNotification;
use App\Models\Fcm;
use App\Models\Order;
use Auth;
use Kreait\Firebase\Factory;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $user=Auth::user();
        $message="Une vente vient d'etre crée par ".$user->name." avec un montant de ".$order->order_total_amount." MRU";
        SendFcmOrderCreatedNotification::dispatch($order, $user->name);
//         $credentialsPath = config('services.firebase.credentials');
// if (!file_exists($credentialsPath)) {
//     logger("Firebase credentials not found at: " . $credentialsPath);
//     return;
// }else{
//     logger("Firebase credentials found at: ". $credentialsPath);
// }
//         $factory = (new Factory())->withServiceAccount(config('services.firebase.credentials'));


        // $messaging = $factory->createMessaging();

        // $token = Fcm::latest()->value('token');
        //             $messages = [
        //         'token' => $token,
        //         'notification' => [
        //             'title' => "Nouvelle vente",
        //             'body' => $message,
        //         ],
        //     ];
        //     $messaging->send($messages);
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
