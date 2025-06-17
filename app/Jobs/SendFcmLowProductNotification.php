<?php

namespace App\Jobs;

use App\Models\Fcm;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Kreait\Firebase\Factory;
use Log;
class SendFcmLowProductNotification implements ShouldQueue
{
    use Queueable,Dispatchable;

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
    public function handle(FirebaseService $firebase): void{
           $tokens = Fcm::pluck('token')->toArray();

        foreach ($tokens as $token) {
            $firebase->sendToToken(

                $token,
                'Faible ',
                $this->message,
            );
        }
    }
}
