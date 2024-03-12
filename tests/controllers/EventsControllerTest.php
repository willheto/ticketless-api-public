<?php

use Tests\TestCase;
use App\Models\Event;
use App\Models\Organization;

class EventsControllerTest extends TestCase
{
    public function testGetSingleEvent(): void
    {
        Event::factory()->create(['eventID' => 99, 'isPublic' => 1, 'status' => 'active']);
        $this->json('GET', 'events/99')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'event' => [
                    'eventID',
                    'name',
                    'location',
                    'type',
                    'date',
                    'image',
                    'trendingScore',
                    'ticketMaxPrice',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function testAllEvents(): void
    {
        $this->json('GET', 'events')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'events' => [
                    '*' => [
                        'eventID',
                        'name',
                        'location',
                        'type',
                        'date',
                        'image',
                        'trendingScore',
                        'ticketMaxPrice',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function testAllPublicActiveEvents(): void
    {
        $response = $this->json('GET', 'events');

        $response->seeStatusCode(200)
            ->seeJsonStructure([
                'events' => [
                    '*' => [
                        'eventID',
                        'name',
                        'location',
                        'type',
                        'date',
                        'image',
                        'trendingScore',
                        'ticketMaxPrice',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        $data = json_decode($response->response->getContent(), true);

        foreach ($data['events'] as $event) {
            $status = $event['status'];
            $isPublic = $event['isPublic'];

            $this->assertNotTrue(in_array($isPublic, ['0']));
            $this->assertNotTrue(in_array($status, ['inactive']));

            // Should not return scheduled event with activeTo in the past
            if (isset($event['activeTo'])) {
                $this->assertNotEquals('2020-03-03', $event['activeTo']);
            }
        }
    }

    public function testCreateEvent(): void
    {
        $postData = [
            'organizationID' => 1,
            'name' => 'Test event',
            'location' => 'Test location',
            'type' => 'Test type',
            'date' => '2022-12-12',
            'ticketMaxPrice' => 100
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'events', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'event' => [
                    'eventID',
                    'name',
                    'location',
                    'type',
                    'date',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->dontSeeJson([
                'organizationID' => $postData['organizationID'],
                'ticketMaxPrice' => $postData['ticketMaxPrice']
            ]);
    }

    public function testCreateScheduledEventWithMissingRequiredData(): void
    {
        $postData = [
            'name' => 'Test event',
            'location' => 'Test location',
            'type' => 'Test type',
            'date' => '2022-12-12',
            'status' => 'scheduled'
        ];

        $headers = $this->createAuthorizationHeaders($this->getSuperadminUser());

        $this->json('POST', 'events', $postData, $headers)
            ->seeStatusCode(422);
    }

    public function testCreateScheduledEvent(): void
    {
        $postData = [
            'name' => 'Test event',
            'location' => 'Test location',
            'type' => 'Test type',
            'date' => '2022-12-12',
            'status' => 'scheduled',
            'activeFrom' => '2024-03-15 12:00:00',
            'activeTo' => '2024-03-20 14:00:00'
        ];

        $headers = $this->createAuthorizationHeaders($this->getSuperadminUser());

        $this->json('POST', 'events', $postData, $headers)
            ->seeJsonStructure([
                'event' => [
                    'eventID',
                    'name',
                    'location',
                    'type',
                    'date',
                    'activeFrom',
                    'activeTo',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->seeStatusCode(200);
    }


    public function testCreateEventWithMissingRequiredPostData(): void
    {
        $postData = [
            'organizationID' => 1,
            'location' => 'Test location',
            'type' => 'Test type',
            'date' => '2022-12-12',
            'image' => 'Test image',
            'trendingScore' => 1,
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'events', $postData, $headers)
            ->seeStatusCode(422);
    }

    public function testCreateEventWithUnvalidatedDate(): void
    {
        $postData = [
            'organizationID' => 1,
            'name' => 'Test event',
            'location' => 'Test location',
            'type' => 'Test type',
            'date' => '2022-12-12',
            'image' => 'Test image',
            'trendingScore' => 1,
            'ticketMaxPrice' => 1000
        ];

        $headers = $this->createAuthorizationHeaders($this->getTestUser());

        $this->json('POST', 'events', $postData, $headers)
            ->seeStatusCode(422);
    }

    public function testCreateEventWithNoAuthorization(): void
    {
        $postData = [
            'organizationID' => 1,
            'name' => 'Test event',
            'location' => 'Test location',
            'type' => 'Test type',
            'date' => '2022-12-12',
            'image' => 'Test image',
            'trendingScore' => 1,
            'ticketMaxPrice' => 100
        ];

        $this->json('POST', 'events', $postData)
            ->seeStatusCode(401);
    }

    public function testGetEventByTicketID(): void
    {
        $this->json('GET', 'tickets/1/event')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'event' => [
                    'eventID',
                    'name',
                    'location',
                    'type',
                    'date',
                    'image',
                    'trendingScore',
                    'ticketMaxPrice',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function testGetEventByTicketIDWithWrongID(): void
    {
        $this->json('GET', 'tickets/0/event')
            ->seeStatusCode(404);
    }

    public function testGetOrganizationPublicEvents(): void
    {
        Event::factory()->count(5)->create(['organizationID' => 1]);
        Event::factory()->create(['showEventOnCalendar' => false]);
        $this->json('GET', 'organizations/1/events')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'events' => [
                    '*' => [
                        'eventID',
                        'name',
                        'location',
                        'type',
                        'date',
                        'image',
                        'trendingScore',
                        'ticketMaxPrice',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->seeJson([
                'showEventOnCalendar' => true
            ]);
    }
}
