<?php

namespace Tests;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Ticket;


class UserModelTest extends TestCase
{
    public function testHashPassword()
    {
        $user = User::factory()->create();
        $password = 'password';
        $user->hashPassword($password);
        $this->assertNotEquals($password, $user->password);
    }

    public function testIsUserSuperadmin()
    {
        $user = User::factory()->create(['userType' => 'superadmin']);
        $userID = $user->userID;
        $this->assertTrue($user->isUserSuperadmin($userID));
    }

    public function testIsUserNotSuperadmin()
    {
        $user = User::factory()->create(['userType' => 'user']);
        $userID = $user->userID;
        $this->assertFalse($user->isUserSuperadmin($userID));
    }

    public function testIsUserAdmin()
    {
        $user = User::factory()->create(['userType' => 'admin']);
        $userID = $user->userID;
        $this->assertTrue($user->isUserAdmin($userID));
    }

    public function testIsUserNotAdmin()
    {
        $user = User::factory()->create(['userType' => 'user']);
        $userID = $user->userID;
        $this->assertFalse($user->isUserAdmin($userID));
    }

    public function testIsUserUser()
    {
        $user = User::factory()->create(['userType' => 'user']);
        $userID = $user->userID;
        $this->assertTrue($user->isUserUser($userID));
    }

    public function testIsUserNotUser()
    {
        $user = User::factory()->create(['userType' => 'admin']);
        $userID = $user->userID;
        $this->assertFalse($user->isUserUser($userID));
    }

    public function testUploadProfilePicture()
    {
        $user = User::factory()->create();
        $request = new Request();
        $profilePicture = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII';
        $user->uploadNewProfilePicture($profilePicture, $request);
        $this->assertNotNull($request->input('profilePicture'));
    }

    public function testRemoveProfilePicture()
    {
        $user = User::factory()->create();
        $request = new Request();
        $profilePicture = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII';
        $user->uploadNewProfilePicture($profilePicture, $request);
        $user->deleteExistingProfilePicture($request);

        $this->assertNull($user->profilePicture);
    }
}
