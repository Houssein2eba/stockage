<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class TwilioChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTwilio($notifiable);
        
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        return $twilio->messages->create(
            $notifiable->routeNotificationForTwilio(),
            [
                'from' => config('services.twilio.from'),
                'body' => $message
            ]
        );
    }
}