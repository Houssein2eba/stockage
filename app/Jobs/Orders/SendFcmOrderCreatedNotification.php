<?php

namespace App\Jobs\Orders;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Fcm;
use App\Models\Order;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class SendFcmOrderCreatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
        protected Order $order;
    protected string $userName;
    public function __construct(Order $order, string $userName)
    {
        $this->order = $order;
        $this->userName = $userName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = "Une vente vient d'être créée par {$this->userName} avec un montant de {$this->order->order_total_amount} MRU";

        $credentialsPath = config('services.firebase.credentials');
        if (!file_exists($credentialsPath)) {
            Log::error("Firebase credentials not found at: " . $credentialsPath);
            return;
        }
                $factory = (new Factory())->withServiceAccount($credentialsPath);
        $messaging = $factory->createMessaging();

        $token = Fcm::latest()->value('token');

        if (!$token) {
            Log::warning("No FCM token found.");
            return;
        }
                $notification = [
            'token' => $token,
            'notification' => [
                'title' => "Nouvelle vente",
                'body' => $message,
            ],
        ];

        $messaging->send($notification);
    }
}
