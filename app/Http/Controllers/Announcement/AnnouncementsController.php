<?php

namespace App\Http\Controllers\Announcement;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Exceptions\NotFoundException\NotFoundException;
use App\Exceptions\CustomValidationException\CustomValidationException;
use Illuminate\Validation\ValidationException;

class AnnouncementsController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'announcements';
        $this->CRUD_RESPONSE_OBJECT = 'announcement';
    }

    public function getAllAnnouncements(): JsonResponse
    {
        try {
            $announcements = Announcement::all();
            $response = $this->createResponseData($announcements, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getActiveAnnouncements(): JsonResponse
    {
        try {
            $announcements = Announcement::where('isActive', 1)->get();
            $response = $this->createResponseData($announcements, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function createAnnouncement(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Announcement::getValidationRules([]));
            $announcement = new Announcement();
            $announcement->fill($request->all());
            $announcement->save();
            $response = $this->createResponseData($announcement, 'object');
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

    public function updateAnnouncement(Request $request): JsonResponse
    {
        try {
            $announcementID = $request->json('announcementID');
            $announcement = Announcement::where('announcementID', $announcementID)->first();

            if (!$announcement) {
                throw new NotFoundException('Announcement not found');
            }

            $this->validate($request, Announcement::getValidationRules([]));
            $announcement->fill($request->all());
            $announcement->save();
            $response = $this->createResponseData($announcement, 'object');
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

    public function deleteAnnouncement(Request $request): JsonResponse
    {
        try {
            $announcementID = $request->json('announcementID');
            $announcement = Announcement::where('announcementID', $announcementID)->first();

            if (!$announcement) {
                throw new NotFoundException('Announcement not found');
            }

            $announcement->delete();
            $response = $this->createResponseData($announcement, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
