<?php

use App\Models\Event;
use Tests\TestCase;
use App\Models\Chat;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ChatsControllerTest extends TestCase
{

    public function testGetSingleChat(): void
    {
        Event::factory()->create();
        Ticket::factory()->create(['userID' => 1]);
        Chat::factory()->create(['user1ID' => 1, 'user2ID' => 2]);

        $chatUserPartakesIn = Chat::where('user1ID', 1)->orWhere('user2ID', 1)->first();
        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('GET', 'chats/' . $chatUserPartakesIn->chatID, [], $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'chat' => [
                    'chatID',
                    'user1ID',
                    'user2ID',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }


    public function testGetSingleChatNotPartakingIn(): void
    {
        User::factory()->create(['userID' => 55]);
        Chat::factory()->create(['user1ID' => 2, 'user2ID' => 55]);
        $chatUserDoesntPartakeIn = Chat::where('user1ID', '!=', 1)->where('user2ID', '!=', 1)->first();
        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('GET', 'chats/' . $chatUserDoesntPartakeIn->chatID, [], $headers)
            ->seeStatusCode(401);
    }

    public function testGetAllChatsByUserID(): void
    {
        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('GET', 'users/1/chats', [], $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'chats' => [
                    '*' => [
                        'chatID',
                        'user1ID',
                        'user2ID',
                        'created_at',
                        'updated_at',
                        'user1' => [
                            'userID',
                            'firstName',
                            'lastName',
                            'profilePicture',
                            'created_at',
                        ],
                        'user2' => [
                            'userID',
                            'firstName',
                            'lastName',
                            'profilePicture',
                            'created_at',
                        ]
                    ]
                ]
            ]);
    }

    public function testGetAllChatsByUserIDNotPartakingIn(): void
    {
        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('GET', 'users/2/chats', [], $headers)
            ->seeStatusCode(401);
    }

    public function testCreateChat(): void
    {
        Ticket::factory()->create(['userID' => 2]);
        $randomTicketWhichIsNotPartakenIn = Ticket::where('userID', '!=', 1)->first();
        $postData = [
            'user2ID' => $randomTicketWhichIsNotPartakenIn->userID,
            'ticketID' => $randomTicketWhichIsNotPartakenIn->ticketID,
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'chats', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'user1ID' => 1,
                'user2ID' => $postData['user2ID']
            ]);
    }

    public function testCannotCreateChatWithYourself(): void
    {
        $postData = [
            'user2ID' => 1,
            'ticketID' => 1,
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'chats', $postData, $headers)
            ->seeStatusCode(400);
    }

    public function testCannotStartChatWithUserWhoDoesntOwnTicket(): void
    {
        $randomTicketWhichIsNotPartakenIn = Ticket::where('userID', '!=', 1)->first();
        $userWhoDoesntOwnTicket = User::where('userID', '!=', $randomTicketWhichIsNotPartakenIn->userID)->first();
        $postData = [
            'user2ID' => $userWhoDoesntOwnTicket->userID,
            'ticketID' => $randomTicketWhichIsNotPartakenIn->ticketID,
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'chats', $postData, $headers)
            ->seeStatusCode(400);
    }

    public function testUpdateChat(): void
    {
        $chat = Chat::where('user1ID', 1)->orWhere('user2ID', 1)->where('isActive', true)->first();
        $postData = [
            'chatID' => $chat->chatID,
            'isActive' => false,
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('PATCH', 'chats', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'chatID' => $chat->chatID,
                'isActive' => false
            ]);
    }

    public function testDontAllowChangingChatTicketID(): void
    {
        $chat = Chat::where('user1ID', 1)->orWhere('user2ID', 1)->first();
        $postData = [
            'chatID' => $chat->chatID,
            'ticketID' => 999,
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('PATCH', 'chats', $postData, $headers)
            ->seeStatusCode(422);
    }

    public function testDontAllowChangingChatUserIDs(): void
    {
        $chat = Chat::where('user1ID', 1)->orWhere('user2ID', 1)->first();
        $postData = [
            'chatID' => $chat->chatID,
            'user1ID' => 999,
            'user2ID' => 999,
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('PATCH', 'chats', $postData, $headers)
            ->seeStatusCode(422);
    }
}
