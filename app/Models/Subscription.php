<?php

namespace App\Models;

use App\Exceptions\BadRequestException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Minishlink\WebPush\WebPush;

class Subscription extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'subscriptionID';

    /**
     * @var string
     */
    protected $foreignKey = 'userID';

    protected $fillable = [
        'userID',
        'endpoint',
        'publicKey',
        'authToken',
    ];

    public function sendNotification(string $title, string $body, int $chatID, string $baseUrl): void
    {
        $webPush = new WebPush([
            'VAPID' => [
                'subject' => 'www.ticketless.fi',
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ]);

        $notification = [
            'title' => $title,
            'body' => $body,
            'chatID' => $chatID,
            'baseUrl' => $baseUrl
        ];

        $subscription = \Minishlink\WebPush\Subscription::create([
            'endpoint' => $this->endpoint,
            'publicKey' => $this->publicKey,
            'authToken' => $this->authToken,
            'contentEncoding' => 'aesgcm',
        ]);



        $webPush->queueNotification(
            $subscription,
            // @phpstan-ignore-next-line
            json_encode($notification)
        );

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                echo "Success: Notification sent to {$endpoint}";
            } else {
                echo "Error: Notification not sent to {$endpoint}: {$report->getReason()}";
            }
        }
    }

    public function checkIfSubscriptionExists(): void
    {
        $subscription = Subscription::where('endpoint', $this->endpoint)->first();
        if ($subscription) {
            throw new BadRequestException('Subscription already exists');
        }
    }
}
