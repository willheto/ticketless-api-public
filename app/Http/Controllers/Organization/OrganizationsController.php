<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Organization;
use App\Exceptions\NotFoundException\NotFoundException;
use Illuminate\Http\Request;
use App\Exceptions\CustomValidationException\CustomValidationException;
use Illuminate\Validation\ValidationException;

class OrganizationsController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'organizations';
        $this->CRUD_RESPONSE_OBJECT = 'organization';
    }

    public function getSingleOrganization(int $organizationID): JsonResponse
    {
        try {
            $organization = Organization::where('organizationID', $organizationID)->first();
            if (!$organization) {
                throw new NotFoundException('Organization not found');
            }

            $response = $this->createResponseData($organization, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getAllOrganizations(): JsonResponse
    {
        try {
            $organizations = Organization::all();
            $response = $this->createResponseData($organizations, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function createOrganization(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Organization::getValidationRules([]));
            $organization = Organization::create($request->all());
            $response = $this->createResponseData($organization, 'object');
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

    public function updateOrganization(Request $request): JsonResponse
    {
        try {
            $this->validate($request, Organization::getValidationRules($request->json()->all()));
            $organizationID = $request->json('organizationID');
            $organization = Organization::where('organizationID', $organizationID)->first();

            if (!$organization) {
                throw new NotFoundException('Organization not found');
            }

            $organization->update($request->except('organizationID'));
            $response = $this->createResponseData($organization, 'object');
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

    public function deleteOrganization(Request $request): JsonResponse
    {
        try {
            $organization = Organization::where('organizationID', $request->organizationID)->first();
            if (!$organization) {
                throw new NotFoundException('Organization not found');
            }

            $organization->delete();
            return response()->json(['success' => 'Organization deleted']);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
