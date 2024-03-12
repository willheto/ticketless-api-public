<?php

namespace App\Http\Controllers\User;

use App\Exceptions\BadRequestException;
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
use App\Managers\EmailManager;
use App\Exceptions\NotFoundException\NotFoundException;

class UserAuthController extends BaseController
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

    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $email = $request->json('email');
            $user = User::where('email', $email)->first();

            if (!$user) {
                throw new NotFoundException('User not found');
            }

            $code = rand(100000, 999999);
            $user->passwordCode = $code;
            $user->save();

            $emailManager = new EmailManager();
            $emailManager->sendForgotPasswordEmail($email, $code);

            return response()->json(['message' => 'Email sent'], 200);
        } catch (Exception $e) {
            if ($e->getCode() === 550) {
                return response()->json(['message' => 'Email sent'], 200);
            }
            return $this->handleError($e);
        }
    }

    public function checkCode(Request $request): JsonResponse
    {
        try {
            $code = $request->json('code');
            $user = User::where('passwordCode', $code)->first();

            if (!$user || $user->passwordCode !== $code) {
                throw new BadRequestException('Invalid code');
            }

            $authManager = new AuthManager();
            $jwt = $authManager->createUserJwt($user->userID);

            $response = [
                'user' => $user->toArray(),
                'token' => $jwt
            ];

            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function checkPassword(Request $request): JsonResponse
    {
        try {
            $userID = $request->json('userID');
            $this->verifyAccessToResource($userID, $request);

            $password = $request->json('password');
            $user = User::where('userID', $userID)->first();

            if (!$user) {
                throw new NotFoundException('User not found');
            }

            if (!password_verify($password, $user->password)) {
                throw new BadRequestException('Password is incorrect', 400);
            }

            return response()->json(['isValid' => true]);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
