<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Event;
use App\Exceptions\NotFoundException\NotFoundException;
use Illuminate\Http\Request;
use App\Exceptions\CustomValidationException\CustomValidationException;
use App\Exceptions\UnauthorizedException\UnauthorizedException;
use App\Models\Ticket;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class EventsController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'events';
        $this->CRUD_RESPONSE_OBJECT = 'event';
    }

    public function getSingleEvent(int $eventID): JsonResponse
    {
        try {
            $event = Event::where('eventID', $eventID)->active()->first();
            if (!$event) {
                throw new NotFoundException('Event not found');
            }

            $event['ticketsSellingCount'] = $event->getTicketsSellingCount();
            $event['ticketsBuyingCount'] = $event->getTicketsBuyingCount();

            $response = $this->createResponseData($event, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getAllEvents(): JsonResponse
    {
        try {
            $events = Event::all();

            foreach ($events as $event) {
                $event['ticketsSellingCount'] = $event->getTicketsSellingCount();
                $event['ticketsBuyingCount'] = $event->getTicketsBuyingCount();
            }

            $response = $this->createResponseData($events, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function createEvent(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Event::getValidationRules([]));

            $event = new Event();
            $event->fill($event->getFillableEventDataFromRequest($request));
            $event->save();

            $response = $this->createResponseData($event, 'object');
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


    public function updateEvent(Request $request): JsonResponse
    {
        try {
            $eventID = $request->json('eventID');
            $event = Event::where('eventID', $eventID)->first();

            if (!$event) {
                throw new NotFoundException('Event not found');
            }

            $this->validate($request, Event::getValidationRules($request->json()->all()));
            $event->fill($event->getFillableEventDataFromRequest($request));
            $event->save();

            $response = $this->createResponseData($event, 'object');
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

    public function deleteEvent(Request $request): JsonResponse
    {
        try {
            $eventID = $request->json('eventID');
            $event = Event::where('eventID', $eventID)->first();
            $userID = $request->json('userID');

            if (!$event) {
                throw new NotFoundException('Event not found');
            }

            if (User::isUserAdmin($userID)) {
                $user = User::where('userID', $userID)->first();
                if (!$user) {
                    throw new UnauthorizedException();
                }

                if ($event->organizationID !== $user->organizationID) {
                    throw new UnauthorizedException();
                }
            }

            $event->delete();
            return response()->json(['message' => 'Event deleted successfully']);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getEventByTicketID(int $ticketID): JsonResponse
    {
        try {
            $ticket = Ticket::where('ticketID', $ticketID)->first();

            if (!$ticket) {
                throw new NotFoundException('Ticket not found');
            }

            $event = Event::where('eventID', $ticket->eventID)->first();

            if (!$event) {
                throw new NotFoundException('Event not found');
            }

            $response = $this->createResponseData($event, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getActivePublicEvents(): JsonResponse
    {
        try {
            $events = Event::getActivePublicEvents();

            foreach ($events as $event) {
                /** @phpstan-ignore-next-line */
                $event['ticketsSellingCount'] = $event->getTicketsSellingCount();
                /** @phpstan-ignore-next-line */
                $event['ticketsBuyingCount'] = $event->getTicketsBuyingCount();
            }
            $response = $this->createResponseData($events, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }


    public function getOrganizationPublicEvents(int $organizationID): JsonResponse
    {
        try {
            $events = Event::where('organizationID', $organizationID)->where('showEventOnCalendar', true)->get();
            $response = $this->createResponseData($events, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getOrganizationEvents(Request $request): JsonResponse
    {
        try {
            $userID = $request->userID;
            $user = User::where('userID', $userID)->first();

            if (!$user || !$user->organizationID) {
                throw new UnauthorizedException('Unauthorized or no organization ID');
            }

            $events = Event::where('organizationID', $user->organizationID)->get();
            $response = $this->createResponseData($events, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
