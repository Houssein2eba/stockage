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
        Log::info('creating job '.$message.'/');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
{
    $credentialsPath = config('services.firebase.credentials');
if (!file_exists($credentialsPath)) {
    logger("Firebase credentials not found at: " . $credentialsPath);
    return;
}else{
    logger("Firebase credentials found at: ". $credentialsPath);
}
        $factory = (new Factory)->withServiceAccount(config('services.firebase.credentials'));


        $messaging = $factory->createMessaging();

        $token = Fcm::latest()->value('token');
        Log::info("token: ".$token);
                    $messages = [
                'token' => $token,
                'notification' => [
                    'title' => "low product",
                    'body' => $this->message,
                ],
            ];
            $messaging->send($messages);

}
}
