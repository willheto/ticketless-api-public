<?php

namespace App\Http\Controllers\Meta;

use App\Exceptions\CustomValidationException\CustomValidationException;
use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\User;
use App\Models\Advertisement;
use App\Models\Chat;
use App\Models\Message;

class MetaController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_OBJECT = 'meta';
    }

    public function getSuperadminMeta(): JsonResponse
    {
        try {
            $meta = [
                'organizations' => [
                    'count' => 1,
                ],
                'users' => [
                    'count' => User::count(),
                ],
                'events' => [
                    'count' => Event::count(),
                ],
                'tickets' => [
                    'count' => Ticket::count(),
                ],
                'chats' => [
                    'count' => Chat::count(),
                ],
                'messages' => [
                    'count' => Message::count(),
                ],
                'advertisements' => [
                    'count' => Advertisement::count(),
                ],

            ];

            $response = $this->createResponseData($meta, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
