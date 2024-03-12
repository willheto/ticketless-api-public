<?php

namespace App\Http\Controllers\Ticket;

use App\Exceptions\BadRequestException;
use App\Exceptions\CustomValidationException\CustomValidationException;
use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;
use App\Exceptions\NotFoundException\NotFoundException;
use App\Exceptions\UnauthorizedException\UnauthorizedException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Event;

class TicketsController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'tickets';
        $this->CRUD_RESPONSE_OBJECT = 'ticket';
    }

    public function getSingleTicket(int $ticketID): JsonResponse
    {
        try {
            $ticket = Ticket::where('ticketID', $ticketID)->first();
            if (!$ticket) {
                throw new NotFoundException('Ticket not found');
            }

            $response = $this->createResponseData($ticket, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getAllTickets(): JsonResponse
    {
        try {
            $tickets = Ticket::all();
            $response = $this->createResponseData($tickets, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function updateTicket(Request $request): JsonResponse
    {
        try {
            $ticketID = $request->json('ticketID');
            $ticket = Ticket::where('ticketID', $ticketID)->first();

            if (!$ticket) {
                throw new NotFoundException('Ticket not found');
            }

            $postData = $request->json()->all();
            $this->validate($request, Ticket::getValidationRules($postData));

            $userIDFromRequest = $request->json('userID');
            $ticketUserID = $ticket->userID;
            $user = User::where('userID', $userIDFromRequest)->first();

            if (!$user) {
                throw new NotFoundException('User not found');
            }

            if (User::isUserSuperadmin($userIDFromRequest) === false) {
                if (User::isUserAdmin($userIDFromRequest)) {
                    $eventID = $ticket->eventID;
                    $event = Event::where('eventID', $eventID)->first();

                    if (!$event) {
                        throw new NotFoundException('Event not found');
                    }

                    $organizationID = $event->organizationID;

                    if (!$organizationID) {
                        throw new NotFoundException('Organization not found');
                    }

                    if ($user->organizationID !== $organizationID) {
                        throw new UnauthorizedException();
                    }

                    $this->verifyOrganizationAccessToResource($organizationID, $request);
                } else {
                    $this->verifyAccessToResource($ticketUserID, $request);
                }
            }

            if ($request->json('requiresMembership') === false) {
                $ticket->association = null;
            }

            $ticket->update($request->except('userID', 'isSelling'));
            $response = $this->createResponseData($ticket, 'object');
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

    public function createTicket(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Ticket::getValidationRules([]));

            $userID = $request->json('userID');
            $eventID = $request->json('eventID');
            
            if (Event:: isEventEligibleForTicketCreation($eventID) === false) {
                throw new BadRequestException('Event is not active');
            }

            $this->verifyAccessToResource($userID, $request);

            $ticket = new Ticket();
            $ticket->fill($request->all());
            $ticket->save();
            $response = $this->createResponseData($ticket, 'object');
            return response()->json($response, 201);
        } catch (ValidationException $e) {
            if ($e->getMessage()) {
                return $this->handleError(new CustomValidationException($e->getMessage()));
            }
            return $this->handleError(new CustomValidationException());
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function deleteTicket(Request $request): JsonResponse
    {
        try {
            $ticketID = $request->json('ticketID');
            if (!$ticketID) {
                throw new CustomValidationException('TicketID is required');
            }

            $ticket = Ticket::where('ticketID', $ticketID)->first();

            if (!$ticket) {
                throw new NotFoundException('Ticket not found');
            }

            $userIDFromRequest = $request->json('userID');
            $ticketUserID = $ticket->userID;
            $user = User::where('userID', $userIDFromRequest)->first();

            if (!$user) {
                throw new NotFoundException('User not found');
            }

            if (User::isUserSuperadmin($userIDFromRequest) === false) {
                if (User::isUserAdmin($userIDFromRequest)) {
                    $eventID = $ticket->eventID;
                    $event = Event::where('eventID', $eventID)->first();

                    if (!$event) {
                        throw new NotFoundException('Event not found');
                    }

                    $organizationID = $event->organizationID;

                    if (!$organizationID) {
                        throw new NotFoundException('Organization not found');
                    }

                    if ($user->organizationID !== $organizationID) {
                        throw new UnauthorizedException();
                    }

                    $this->verifyOrganizationAccessToResource($organizationID, $request);
                } else {
                    $this->verifyAccessToResource($ticketUserID, $request);
                }
            }

            $ticket->delete();
            return response()->json(['success' => 'Ticket deleted'], 200);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getTicketsByEventID(int $eventID): JsonResponse
    {
        try {
            $tickets = Ticket::where('eventID', $eventID)->get();
            $response = $this->createResponseData($tickets, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getTicketsByUserID(int $userID): JsonResponse
    {
        try {
            $tickets = Ticket::where('userID', $userID)->get();
            $response = $this->createResponseData($tickets, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getTicketsByOrganizationID(int $organizationID): JsonResponse
    {
        try {
            $organizationEvents = Event::where('organizationID', $organizationID)->get();

            if ($organizationEvents->isEmpty()) {
                throw new NotFoundException('Organization events not found');
            }

            $organizationEventsIDs = $organizationEvents->pluck('eventID')->toArray();
            $tickets = Ticket::whereIn('eventID', $organizationEventsIDs)->get();

            $response = $this->createResponseData($tickets, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
