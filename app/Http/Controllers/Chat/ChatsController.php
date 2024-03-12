<?php

namespace App\Http\Controllers\Chat;

use App\Exceptions\CustomValidationException\CustomValidationException;
use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Chat;
use App\Exceptions\NotFoundException\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatsController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'chats';
        $this->CRUD_RESPONSE_OBJECT = 'chat';
    }

    public function getSingleChat(int $chatID, Request $request): JsonResponse
    {
        try {
            $chat = Chat::where('chatID', $chatID)
                ->with('user1', 'user2', 'ticket', 'messages')
                ->first();

            if (!$chat) {
                throw new NotFoundException('Chat not found');
            }

            $this->verifyAccessToChat($chat, $request);

            $response = $this->createResponseData($chat, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function updateChat(Request $request): JsonResponse
    {
        try {
            $chatID = $request->json('chatID');
            $chat = Chat::where('chatID', $chatID)->first();

            if (!$chat) {
                throw new NotFoundException('Chat not found');
            }

            $postData = $request->json()->all();
            $this->validate($request, Chat::getValidationRules($postData));
            $this->verifyAccessToChat($chat, $request);

            $chat->update($request->except('chatID', 'user1ID', 'user2ID', 'ticketID'));
            $response = $this->createResponseData($chat, 'object');
            return response()->json($response);
        } catch (ValidationException $e) {
            if ($e->getMessage()) {
                return $this->handleError(new CustomValidationException($e->getMessage()));
            }
            return $this->handleError(new CustomValidationException());
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function createChat(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Chat::getValidationRules([
                'user2ID',
                'ticketID'
            ]));

            // user1ID is the user who is creating the chat
            $userID = $request->json('userID');

            $chat = new Chat();
            $chat->user1ID = $userID;
            $chat->user2ID = $request->json('user2ID');
            $chat->ticketID = $request->json('ticketID');

            $chat->verifyChatDoesNotAlreadyExist();
            $chat->verifyNotStartingChatWithSelf();
            $chat->verifyRelatedTicketExists();
            $chat->verifyUser2IsRelatedToTicket();

            $chat->save();
            $response = $this->createResponseData($chat, 'object');
            return response()->json($response);
        } catch (ValidationException $e) {
            if ($e->getMessage()) {
                return $this->handleError(new CustomValidationException($e->getMessage()));
            }
            return $this->handleError(new CustomValidationException());
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getChatsByEventID(int $eventID): JsonResponse
    {
        try {
            $chats = Chat::where('eventID', $eventID)->get();
            $response = $this->createResponseData($chats, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getChatsByUserID(int $userID, Request $request): JsonResponse
    {
        try {
            $this->verifyAccessToResource($userID, $request);
            $chats = Chat::where('user1ID', $userID)
                ->orWhere('user2ID', $userID)
                ->with('messages', 'user1', 'user2')
                ->get();

            $response = $this->createResponseData($chats, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
