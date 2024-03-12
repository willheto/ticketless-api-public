<?php

use Tests\TestCase;

use App\Models\Ticket;
use App\Models\Event;

class TicketsControllerTest extends TestCase
{
    public function testGetSingleTicket(): void
    {
        $this->json('GET', 'tickets/1')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'ticket' => [
                    'userID',
                    'ticketID',
                    'eventID',
                    'header',
                    'description',
                    'price',
                    'quantity',
                    'association',
                    'isSelling',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function testGetAllTickets(): void
    {
        $this->json('GET', 'tickets')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'tickets' => [
                    '*' => [
                        'userID',
                        'ticketID',
                        'eventID',
                        'header',
                        'description',
                        'price',
                        'quantity',
                        'association',
                        'isSelling',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function testCreateTicketWithoutAuth(): void
    {
        $postData = [
            'userID' => 1,
            'eventID' => 1,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => true
        ];

        $this->json('POST', 'tickets', $postData)
            ->seeStatusCode(401);
    }

    public function testCreateTicketWithAuth(): void
    {
        $event = Event::where('status', 'active')->first();
        $postData = [
            'userID' => 1,
            'eventID' => $event->eventID,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => true
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(201)
            ->seeJson([
                'userID' => $postData['userID'],
                'eventID' => $postData['eventID'],
                'header' => $postData['header'],
                'description' => $postData['description'],
                'price' => $postData['price'],
                'quantity' => $postData['quantity'],
                'association' => $postData['association'],
                'isSelling' => $postData['isSelling']
            ]);
    }

    public function testCreateTicketAsOtherUser(): void
    {
        $event = Event::where('status', 'active')->first();
        $postData = [
            'userID' => 2,
            'eventID' => $event->eventID,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => true
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());
        $userID = $this->getTestUser()['userID'];

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(201)
            ->seeJson([
                'userID' => $userID,
                'eventID' => $postData['eventID'],
                'header' => $postData['header'],
                'description' => $postData['description'],
                'price' => $postData['price'],
                'quantity' => $postData['quantity'],
                'association' => $postData['association'],
                'isSelling' => $postData['isSelling']
            ]);
    }

    public function testCreateTicketOnInactiveEvent(): void
    {
        Event::factory()->create(['status' => 'inactive']);
        $event = Event::where('status', 'inactive')->first();

        $postData = [
            'userID' => 1,
            'eventID' =>  $event->eventID,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => true
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(400);
    }

    public function testCreateTicketOnScheduledNotActiveEvent(): void
    {
        Event::factory()->create(['status' => 'scheduled', 'activeTo' => '2020-03-03']);
        $event = Event::where('status', 'scheduled')->where('activeTo', '2020-03-03')->first();

        $postData = [
            'userID' => 1,
            'eventID' =>  $event->eventID,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => true
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(400);
    }

    public function testCreateTicketOnScheduledActiveEvent(): void
    {
        Event::factory()->create(['status' => 'scheduled', 'activeFrom' => '2020-03-03', 'activeTo' => '2099-01-01']); 
        $event = Event::where('status', 'scheduled')->where('activeTo', '2099-01-01')->first();

        $postData = [
            'userID' => 1,
            'eventID' =>  $event->eventID,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => true
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(201)
            ->seeJson([
                'userID' => $postData['userID'],
                'eventID' => $postData['eventID'],
                'header' => $postData['header'],
                'description' => $postData['description'],
                'price' => $postData['price'],
                'quantity' => $postData['quantity'],
                'association' => $postData['association'],
                'isSelling' => $postData['isSelling']
            ]);
    }

    public function testCreateTicketOnRedirectEvent(): void
    {
        Event::factory()->create(['status' => 'redirect']);
        $inActiveEvent = Event::where('status', 'redirect')->first();

        $postData = [
            'userID' => 1,
            'eventID' =>  $inActiveEvent->eventID,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => true
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(400);
    }

    public function testCreateTicketThatDoesntPassValidation(): void
    {
        $postData = [
            'userID' => 1,
            'eventID' => 1,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 0,
            'association' => 'Test association',
            'isSelling' => true
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(422);

        $postData = [
            'userID' => 1,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => 'true'
        ];

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(422);

        $postData = [
            'eventID' => 1,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => 'true'
        ];

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(422);

        $postData = [
            'userID' => 1,
            'eventID' => 1,
            'header' => 'Test ticket',
            'description' => 'Test description',
            'price' => 1001,
            'quantity' => 1,
            'association' => 'Test association',
            'isSelling' => 'true'
        ];

        $this->json('POST', 'tickets', $postData, $headers)
            ->seeStatusCode(422);
    }

    public function testUpdateTicket(): void
    {
        $ticketUserOwns = Ticket::where('userID', $this->getTestUser()['userID'])->first();
        $postData = [
            'ticketID' => $ticketUserOwns->ticketID,
            'header' => 'Updated ticket',
            'description' => 'Updated description',
            'price' => 2,
            'quantity' => 2,
            'association' => 'Updated association',
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('PATCH', 'tickets', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'ticketID' => $postData['ticketID'],
                'header' => $postData['header'],
                'description' => $postData['description'],
                'price' => $postData['price'],
                'quantity' => $postData['quantity'],
                'association' => $postData['association'],
            ]);
    }

    public function testTransferingTicketOtherUserShouldFail(): void
    {
        $ticketUserOwns = Ticket::where('userID', $this->getTestUser()['userID'])->first();

        $postData = [
            'ticketID' => $ticketUserOwns->ticketID,
            'userID' => 2
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('PATCH', 'tickets', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'ticketID' => $postData['ticketID'],
                'userID' => $this->getTestUser()['userID']
            ]);
    }

    public function testIsSellingCannotBeUpdated(): void
    {
        $ticketUserOwns = Ticket::where('userID', $this->getTestUser()['userID'])->first();

        $postData = [
            'ticketID' => $ticketUserOwns->ticketID,
            'isSelling' => $ticketUserOwns->isSelling ? false : true
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('PATCH', 'tickets', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'ticketID' => $postData['ticketID'],
                'isSelling' => $ticketUserOwns->isSelling
            ]);
    }

    public function testDontAllowUpdateWithoutAuth(): void
    {
        $postData = [
            'ticketID' => 1,
            'header' => 'Updated ticket',
            'description' => 'Updated description',
            'price' => 2,
            'quantity' => 2,
            'association' => 'Updated association',
            'isSelling' => false
        ];

        $this->json('PATCH', 'tickets', $postData)
            ->seeStatusCode(401);
    }

    public function testDeleteTicket(): void
    {
        $ticketUserOwns = Ticket::where('userID', $this->getTestUser()['userID'])->first();
        $postData = [
            'ticketID' => $ticketUserOwns->ticketID
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('DELETE', 'tickets', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'success' => 'Ticket deleted'
            ]);
    }

    public function testDeleteTicketWithoutAuth(): void
    {
        $postData = [
            'ticketID' => 1
        ];

        $this->json('DELETE', 'tickets', $postData)
            ->seeStatusCode(401);
    }

    public function testDeleteTicketThatDoesntExist(): void
    {
        $postData = [
            'ticketID' => 999
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('DELETE', 'tickets', $postData, $headers)
            ->seeStatusCode(404);
    }

    public function testDeleteTicketThatUserDoesntOwn(): void
    {
        $ticket = Ticket::where('userID', '!=', $this->getTestUser()['userID'])->first();
        $postData = [
            'ticketID' => $ticket->ticketID
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('DELETE', 'tickets', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testGetTicketsByEventID(): void
    {
        $this->json('GET', 'events/1/tickets')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'tickets' => [
                    '*' => [
                        'userID',
                        'ticketID',
                        'eventID',
                        'header',
                        'description',
                        'price',
                        'quantity',
                        'association',
                        'isSelling',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }



    public function testGetTicketsByUserID(): void
    {
        $this->json('GET', 'users/1/tickets')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'tickets' => [
                    '*' => [
                        'userID',
                        'ticketID',
                        'eventID',
                        'header',
                        'description',
                        'price',
                        'quantity',
                        'association',
                        'isSelling',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }
}
