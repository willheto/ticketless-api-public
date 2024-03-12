<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Exceptions\CustomValidationException\CustomValidationException;
use App\Exceptions\NotFoundException\NotFoundException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Managers\EmailManager;

class UsersController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'users';
        $this->CRUD_RESPONSE_OBJECT = 'user';
    }

    public function getSingleUserPublicData(int $userID): JsonResponse
    {
        try {
            $user = User::where('userID', $userID)->first();
            if (!$user) {
                throw new NotFoundException('User not found');
            }

            $user = $user->setVisible(['userID', 'firstName', 'lastName', 'profilePicture', 'created_at']);
            $response = $this->createResponseData($user, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getAllUsers(): JsonResponse
    {
        try {
            $users = User::all();
            $response = $this->createResponseData($users, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function createUser(Request $request): JsonResponse
    {
        try {
            $this->validate($request, User::getValidationRules([]));

            $request['password'] = password_hash($request['password'], PASSWORD_BCRYPT);
            $user = User::create($request->except('userType', 'organizationID'));

            $response = $this->createResponseData($user, 'object');
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

    public function deleteUser(Request $request): JsonResponse
    {
        try {
            $userID = $request->json('userToDelete');
            $user = User::where('userID', $userID)->first();

            if (!$user) {
                throw new NotFoundException('User not found');
            }

            $user->delete();
            return response()->json(['message' => 'User deleted']);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function updateUser(Request $request): JsonResponse
    {
        try {

            $this->validate($request, User::getValidationRules($request->json()->all()));

            $userID = $request->json('userID');
            $user = User::where('userID', $userID)->first();

            if (!$user) {
                throw new NotFoundException('User not found');
            }

            $user->update($user->getFillableUserDataFromRequest($request));

            $response = $this->createResponseData($user, 'object');
            return response()->json($response);
        } catch (ValidationException $e) {
            return $this->handleError(new CustomValidationException);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function superadminUpdateUser(Request $request): JsonResponse
    {
        try {
            $postData = $request->json()->all();
            $this->validate($request, User::getValidationRules($postData));

            $userID = $request->json('userToUpdate');
            if (!$userID) {
                throw new CustomValidationException('userToUpdate is required');
            }

            $user = User::where('userID', $userID)->first();

            if (!$user) {
                throw new NotFoundException('User not found');
            }
            if ($request->json('userType') === 'user') {
                $request['organizationID'] = null;
            }

            $user->update($request->except('userID', 'profilePicture', 'password'));
            $response = $this->createResponseData($user, 'object');
            return response()->json($response);
        } catch (ValidationException $e) {
            return $this->handleError(new CustomValidationException);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function reportUser(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'reportedID' => ['required', 'integer', 'exists:users,userID'],
                'description' => 'required|string|max:1000',
            ]);

            $reporterID = $request->json('userID');
            $reporter = User::where('userID', $reporterID)->first();

            if (!$reporter) {
                throw new NotFoundException('Reporter not found');
            }

            $reporterName = $reporter->firstName . ' ' . $reporter->lastName;
            $reported = User::where('userID', $request->json('reportedID'))->first();
            if (!$reported) {
                throw new NotFoundException('Reported user not found');
            }
            $reportedName = $reported->firstName . ' ' . $reported->lastName;
            $content = $request->json('description');

            $emailManager = new EmailManager();
            $emailManager->sendReportEmail($reportedName, $reporterName, $content);

            return response()->json(['message' => 'Report sent']);
        } catch (ValidationException $e) {
            return $this->handleError(new CustomValidationException);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
