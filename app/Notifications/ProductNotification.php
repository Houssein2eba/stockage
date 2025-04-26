<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProductNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['twilio'];
    }

    public function toTwilio(object $notifiable): string
    {
        return "New Product Alert!\n\n" .
               "Name: {$this->product->name}\n" .
               "Price: {$this->product->price}\n" .
               "Description: {$this->product->description}\n\n" .
               "Created at: " . now()->format('M j, Y g:i A');
    }
}