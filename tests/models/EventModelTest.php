<?php

namespace Tests;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Ticket;


class EventModelTest extends TestCase
{

    public function testGetValidationRules()
    {
        $rules = Event::getValidationRules([]);
        $this->assertIsArray($rules);
    }

    public function testGetTicketsSellingCount()
    {
        $event = Event::factory()->create();
        Ticket::factory()->create(['eventID' => $event->eventID, 'isSelling' => 1]);

        $this->assertEquals(1, $event->getTicketsSellingCount());
    }

    public function testGetTicketsBuyingCount()
    {
        $event = Event::factory()->create();
        Ticket::factory()->create(['eventID' => $event->eventID, 'isSelling' => 0]);

        $this->assertEquals(1, $event->getTicketsBuyingCount());
    }

    public function testIsEventEligibleForTicketCreation()
    {
        $event = Event::factory()->create(['status' => 'active']);

        $this->assertTrue(Event::isEventEligibleForTicketCreation($event->eventID));
    }

    public function testInactiveEventIsNotEligibleForTicketCreation()
    {
        $event = Event::factory()->create(['status' => 'inactive']);

        $this->assertFalse(Event::isEventEligibleForTicketCreation($event->eventID));
    }

    public function testRedirectEventIsNotEligibleForTicketCreation()
    {
        $event = Event::factory()->create(['status' => 'redirect']);

        $this->assertFalse(Event::isEventEligibleForTicketCreation($event->eventID));
    }
}
