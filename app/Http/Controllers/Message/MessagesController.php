<?php

namespace App\Http\Controllers\Message;

use App\Exceptions\BadRequestException;
use App\Exceptions\CustomValidationException\CustomValidationException;
use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Message;
use App\Exceptions\NotFoundException\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Chat;

class MessagesController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'messages';
        $this->CRUD_RESPONSE_OBJECT = 'message';
    }

    public function createMessage(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Message::getValidationRules([]));

            $chat = Chat::where('chatID', $request->json('chatID'))->first();
            if (!$chat) {
                throw new BadRequestException('Chat not found');
            }

            // make sure the user is part of the chat
            $this->verifyAccessToChat($chat, $request);

            // make sure receiverID is part of the chat
            $receiverID = $request->json('receiverID');
            if ($chat->user1ID != $receiverID && $chat->user2ID != $receiverID) {
                throw new BadRequestException('Receiver is not part of the chat');
            }

            $message = new Message();
            $message->senderID = $request->json('userID');
            $message->receiverID = $request->json('receiverID');
            $message->chatID = $request->json('chatID');
            $message->content = $request->json('content');

            $message->save();
            $response = $this->createResponseData($message, 'object');
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

    public function userHasUnreadMessages(Request $request): JsonResponse
    {
        try {
            $userID = $request->json('userID');
            $messages = Message::where('receiverID', $userID)->where('isRead', false)->get();

            if (count($messages) > 0) {
                $response = $this->createResponseData(['isUnreadMessages' => true], 'object');
                return response()->json($response);
            }
            $response = $this->createResponseData(['isUnreadMessages' => false], 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getAllMessagesByChatID(int $chatID, Request $request): JsonResponse
    {
        try {
            $chat = Chat::where('chatID', $chatID)->first();
            if (!$chat) {
                throw new NotFoundException('Chat not found');
            }

            $this->verifyAccessToChat($chat, $request);

            $messages = Message::where('chatID', $chatID)->get();
            $response = $this->createResponseData($messages, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function markMessagesAsRead(int $chatID, Request $request): JsonResponse
    {
        try {
            $userID = $request->json('userID');
            $messages = Message::where('chatID', $chatID)->where('receiverID', $userID)->get();
            foreach ($messages as $message) {
                $message->isRead = true;
                $message->save();
            }
            $chat = Chat::where('chatID', $chatID)
                ->with('messages', 'user1', 'user2')
                ->first();
            $response = $this->createResponseData($chat, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
