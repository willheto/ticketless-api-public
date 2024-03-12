<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Advertisement;
use App\Exceptions\NotFoundException\NotFoundException;
use Illuminate\Http\Request;
use App\Exceptions\CustomValidationException\CustomValidationException;
use Illuminate\Validation\ValidationException;

class AdvertisementsController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'advertisements';
        $this->CRUD_RESPONSE_OBJECT = 'advertisement';
    }

    public function getActiveAdvertisements(): JsonResponse
    {
        try {
            $advertisements = Advertisement::where('isActive', 1)->get();
            $response = $this->createResponseData($advertisements, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getAllAdvertisements(): JsonResponse
    {
        try {
            $advertisements = Advertisement::all();
            $response = $this->createResponseData($advertisements, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function viewAdvertisement(int $advertisementID): JsonResponse
    {
        try {
            $advertisement = Advertisement::where('advertisementID', $advertisementID)->first();
            if (!$advertisement) {
                throw new NotFoundException('Advertisement not found');
            }
            $advertisement->views++;
            $advertisement->save();
            $response = $this->createResponseData($advertisement, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function clickAdvertisement(int $advertisementID): JsonResponse
    {
        try {
            $advertisement = Advertisement::where('advertisementID', $advertisementID)->first();
            if (!$advertisement) {
                throw new NotFoundException('Advertisement not found');
            }
            $advertisement->clicks++;
            $advertisement->save();
            $response = $this->createResponseData($advertisement, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function createAdvertisement(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Advertisement::getValidationRules([]));
            $advertisement = Advertisement::create($request->except('views', 'clicks'));
            $response = $this->createResponseData($advertisement, 'object');
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

    public function updateAdvertisement(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Advertisement::getValidationRules($request->json()->all()));

            $advertisement = Advertisement::where('advertisementID', $request->advertisementID)->first();
            if (!$advertisement) {
                throw new NotFoundException('Advertisement not found');
            }

            $advertisement->update($request->except('advertisementID', 'views', 'clicks'));
            $response = $this->createResponseData($advertisement, 'object');
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

    public function deleteAdvertisement(Request $request): JsonResponse
    {
        try {
            $advertisement = Advertisement::where('advertisementID', $request->advertisementID)->first();
            if (!$advertisement) {
                throw new NotFoundException('Advertisement not found');
            }
            $advertisement->delete();
            $response = $this->createResponseData($advertisement, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
