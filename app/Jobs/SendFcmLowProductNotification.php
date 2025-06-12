<?php

namespace App\Jobs;

use App\Models\Fcm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Kreait\Firebase\Factory;
use Log;
class SendFcmLowProductNotification implements ShouldQueue
{
    use Queueable;

    protected String $message;

    /**
     * Create a new job instance.
     */
    public function __construct(String $message)
    {
        $this->message = $message;
        Log::info('creating job');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
{
    Log::info('sending notification');
    $credentialsPath = config('services.firebase.credentials');

    if (!file_exists($credentialsPath)) {
        logger("Firebase credentials not found at: $credentialsPath");
        return;
    }

    $factory = (new Factory)->withServiceAccount($credentialsPath);
    $messaging = $factory->createMessaging();

    // Get all valid tokens, not just the latest one
    $tokens = Fcm::whereNotNull('token')->pluck('token')->toArray();

    if (empty($tokens)) {
        logger('No valid FCM tokens found.');
        return;
    }

    $message = [
        'tokens' => $tokens, // Send to multiple devices
        'notification' => [
            'title' => 'Low Product Alert',
            'body' => $this->message,
        ],
    ];

    try {
        $messaging->send($message);
        Log::info('Notification sent successfully');
    } catch (\Exception $e) {
        Log::error('Failed to send notification: ' . $e->getMessage());

        // Optionally remove invalid tokens
        $failedTokens = [];
        if (method_exists($e, 'failedTokens')) {
            $failedTokens = $e->failedTokens();
        }

        if (!empty($failedTokens)) {
            Fcm::whereIn('token', $failedTokens)->delete();
        }
    }
}
}
