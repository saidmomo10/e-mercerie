<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;

class SendWebPush implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $payload;

    /**
     * Create a new job instance.
     * $payload can be array or string
     */
    public function __construct($payload = [])
    {
        $this->payload = $payload;
    }

    public function handle()
    {
        $vapid = [
            'VAPID' => [
                'subject' => env('WEBPUSH_VAPID_SUBJECT', 'mailto:admin@example.com'),
                'publicKey' => env('WEBPUSH_VAPID_PUBLIC'),
                'privateKey' => env('WEBPUSH_VAPID_PRIVATE'),
            ]
        ];

        $webPush = new WebPush($vapid);

        $subs = PushSubscription::all();

        $payloadJson = is_array($this->payload) ? json_encode($this->payload) : (string) $this->payload;

        foreach ($subs as $s) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $s->endpoint,
                    'keys' => [
                        'p256dh' => $s->public_key,
                        'auth' => $s->auth_token,
                    ],
                ]);
                $webPush->queueNotification($subscription, $payloadJson);
            } catch (\Throwable $e) {
                // Log and continue
                logger()->error('WebPush queue failed', ['error' => $e->getMessage(), 'sub_id' => $s->id]);
            }
        }

        foreach ($webPush->flush() as $report) {
            // You may log results for each notification
            if ($report->isSuccess()) {
                logger()->info('WebPush sent', ['endpoint' => $report->getRequest()->getUri()]);
            } else {
                logger()->warning('WebPush failed', ['endpoint' => $report->getRequest()->getUri(), 'statusCode' => $report->getResponse() ? $report->getResponse()->getStatusCode() : null]);
            }
        }
    }
}
