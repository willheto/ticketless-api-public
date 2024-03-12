<?php

namespace App\Http\Controllers\User;

use App\Exceptions\CustomValidationException\CustomValidationException;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Managers\AuthManager;

class AdminAuthController extends BaseController
{

    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'users';
        $this->CRUD_RESPONSE_OBJECT = 'user';
    }

    public function authenticate(Request $request): JsonResponse
    {
        try {
            $token = $request->json('token');
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $userID = $decoded->userID;
            $user = User::where('userID', $userID)->first();

            if (empty($user)) {
                throw new Exception('User not found', 404);
            }

            if ($user->isUserSuperadmin($user->userID) === false && $user->isUserAdmin($user->userID) === false) {
                throw new Exception('Unauthorized', 401);
            }

            return response()->json(['user' => $user->toArray()]);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'email' => ['email', 'required'],
                'password' => 'required'
            ]);

            $email = $request->json('email');
            $user = User::where('email', $email)->first();

            $password = $request->json('password');
            if (empty($user) || Hash::check($password, $user->password) === false) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            if ($user->isUserSuperadmin($user->userID) === false && $user->isUserAdmin($user->userID) === false) {
                throw new Exception('Unauthorized', 401);
            }

            $authManager = new AuthManager();
            $jwt = $authManager->createUserJwt($user->userID);

            $response = [
                'user' => $user->toArray(),
                'token' => $jwt
            ];

            return response()->json($response);
        } catch (ValidationException $e) {
            $exceptionMessage = $e->getMessage();
            $validationException = new CustomValidationException($exceptionMessage);
            return $this->handleError($validationException);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
