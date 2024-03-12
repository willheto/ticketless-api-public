<?php

namespace App\Managers;

use App\Exceptions\UnauthorizedException\UnauthorizedException;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use App\Models\Chat;
use App\Models\User;

class AuthManager
{

    public function verifyAccess(int $userID, Request $request): void
    {
        $userIDFromToken = $request->userID;
        if ($userID != $userIDFromToken) {
            throw new UnauthorizedException();
        }
    }

    public function verifyAccessToChat(Chat $chat, Request $request): void
    {
        $userID = $request->userID;
        $user1ID = $chat->user1ID;
        $user2ID = $chat->user2ID;

        if ($userID != $user1ID && $userID != $user2ID) {
            throw new UnauthorizedException();
        }
    }

    public function createUserJwt(int $userID): string
    {
        $payload = [
            'sub' => 'https://app.ticketless.fi',
            'iss' => "ticketless-api",
            'iat' => time(),
            'exp' => time() + 60 * 60 * 10,
            'userID' => $userID
        ];

        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
        return $jwt;
    }

    public function verifyOrganizationAccess(int $organizationID, Request $request): void
    {
        $userID = $request->userID;
        $user = User::where('userID', $userID)->first();
        if (!$user) {
            throw new UnauthorizedException();
        }

        if ($user->organizationID != $organizationID) {
            throw new UnauthorizedException();
        }
    }
}
