<?php

namespace App\Http\Controllers\Push;

use App\Exceptions\BadRequestException;
use App\Exceptions\CustomValidationException\CustomValidationException;
use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Subscription;

class PushController extends BaseController
{

    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'subscriptions';
        $this->CRUD_RESPONSE_OBJECT = 'subscription';
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $userID = $request->json('userID');
            $subscription = $request->json('subscription');

            if (!$userID || !$subscription) {
                throw new BadRequestException();
            }

            $subscription = new Subscription(
                [
                    'userID' => $userID,
                    'endpoint' => $subscription['endpoint'],
                    'publicKey' => $subscription['keys']['p256dh'],
                    'authToken' => $subscription['keys']['auth']
                ],
            );

            $subscription->checkIfSubscriptionExists();

            $subscription->save();
            $response = $this->createResponseData($subscription, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getVapidPublicKey(): JsonResponse
    {
        try {
            $publicKey  = env('VAPID_PUBLIC_KEY');

            $publicKeyObject = [
                'publicKey' => $publicKey
            ];
            return response()->json($publicKeyObject);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function sendPushNotification(Request $request): JsonResponse
    {
        try {
            $receiverID = $request->json('receiverID');
            $title = $request->json('senderName');
            $body = $request->json('content');
            $chatID = $request->json('chatID');
            $baseUrl = $request->json('baseUrl');

            if (!$receiverID || !$title || !$body) {
                throw new BadRequestException();
            }

            $subscriptions = Subscription::where('userID', $receiverID)->get();
            if (!$subscriptions) {
                throw new CustomValidationException('No subscriptions found for user');
            }

            foreach ($subscriptions as $subscription) {
                $subscription->sendNotification($title, $body, $chatID, $baseUrl);
            }

            return response()->json(['message' => 'Notification sent']);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
