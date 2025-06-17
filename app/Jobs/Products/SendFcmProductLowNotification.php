<?php

namespace App\Jobs\Products;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Fcm;
use App\Models\Order;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
class SendFcmProductLowNotification implements ShouldQueue
{
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        protected Product $product;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = "Le produit {$this->product->name} est en bas de stock: {$this->product->quantity}";

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
                'title' => "Produit bas de stock",
                'body' => $message,
            ],
        ];

        $messaging->send($notification);
    }
}
