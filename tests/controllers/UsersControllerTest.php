<?php

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UsersControllerTest extends TestCase
{

    public function testSuccessfulUpdate(): void
    {
        $user = $this->getTestUser();

        $postData = [
            'firstName' => 'Bobby',
            'lastName' => 'Tables',
            'email' => 'bobby.tables@ticketless.fi',
            'phoneNumber' => '123456789',
            'city' => 'Helsinki',
            'userType' => 'admin',
            'password' => password_hash('test22', PASSWORD_BCRYPT),
            'language' => 'en',
        ];

        $headers = $this->createAuthorizationHeaders($user);

        $this->json('PATCH', 'users', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'firstName' => $postData['firstName'],
                'lastName' => $postData['lastName'],
                'userID' => $user['userID'],
            ]);
    }

    public function testUpdateWithoutAuth(): void
    {
        $postData = [
            'firstName' => 'Bob changed',
            'lastName' => 'Swift changed',
            'email' => 'bob.swift.changed@ticketless.fi',
            'timeZone' => 'Europe/Stocholm',
            'userType' => 'admin',
            'password' => password_hash('test22', PASSWORD_BCRYPT)
        ];

        $this->json('PATCH', 'users', $postData)
            ->seeStatusCode(401);
    }

    public function testUpdatingUserIDIsNotPossible(): void
    {
        $user = $this->getTestUser();

        $postData = [
            'userID' => 99,
        ];

        $headers = $this->createAuthorizationHeaders($user);

        $this->json('PATCH', 'users', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'userID' => $user['userID'],
            ]);
    }

    public function testGetSingleUser(): void
    {
        $user = $this->getTestUser();

        $response = $this->json('GET', 'users/' . $user['userID'], []);
        $response->seeStatusCode(200)
            ->seeJson([
                'userID' => $user['userID'],
                'firstName' => $user['firstName'],
                'lastName' => $user['lastName'],
            ]);

        $responseData = json_decode($response->response->getContent(), true);
        $this->assertArrayNotHasKey('password', $responseData);
        $this->assertArrayNotHasKey('passwordCode', $responseData);
    }

    public function testCreateUser(): void
    {
        $postData = [
            'firstName' => 'Bob',
            'lastName' => 'Swift',
            'email' => 'test@gmail.com',
            'phoneNumber' => '123456789',
            'city' => 'Helsinki',
            'password' => password_hash('test22', PASSWORD_BCRYPT),
        ];

        $this->json('POST', 'users', $postData)
            ->seeStatusCode(201)
            ->seeJson([
                'firstName' => $postData['firstName'],
                'lastName' => $postData['lastName'],
                'email' => $postData['email'],
                'phoneNumber' => $postData['phoneNumber'],
                'city' => $postData['city'],
            ]);

        $responseData = json_decode($this->response->getContent(), true);
        $this->assertArrayNotHasKey('password', $responseData);
        $this->assertArrayNotHasKey('passwordCode', $responseData);
    }

    public function testDontAllowUserCreateWithHarmfulData(): void
    {
        $postData = [
            'firstName' => 'Bob',
            'lastName' => 'Swift',
            'email' => 'test@gmail.com',
            'organizationID' => 1,
            'password' => password_hash('test22', PASSWORD_BCRYPT),
        ];

        $this->json('POST', 'users', $postData)
            ->seeStatusCode(422);

        $postData = [
            'firstName' => 'Bob',
            'lastName' => 'Swift',
            'email' => 'test@gmail.com',
            'userType' => 'admin',
            'password' => password_hash('test22', PASSWORD_BCRYPT),
        ];

        $this->json('POST', 'users', $postData)
            ->seeStatusCode(422);
    }

    public function testSettingProfilePictureToUser(): void
    {
        $user = $this->getTestUser();

        $postData = [
            'profilePicture' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII'
        ];

        $headers = $this->createAuthorizationHeaders($user);

        $response = $this->json('PATCH', 'users', $postData, $headers);

        $response->seeStatusCode(200);
    }

    public function testRemoveProfilePictureFromUser(): void
    {
        $user = $this->getTestUser();

        $postData = [
            'profilePicture' => null
        ];

        $headers = $this->createAuthorizationHeaders($user);

        $response = $this->json('PATCH', 'users', $postData, $headers);

        $response->seeStatusCode(200);
        $response->seeJson([
            'profilePicture' => null
        ]);
    }

    public function testReportUser(): void
    {
        $user = $this->getTestUser();
        $randomUser = User::where('userID', '!=', $user['userID'])->first();

        $postData = [
            'reportedID' => $randomUser['userID'],
            'description' => 'This is a test report'
        ];

        $headers = $this->createAuthorizationHeaders($user);
        $response = $this->json('POST', 'users/report', $postData, $headers);
        $response->seeStatusCode(200);
    }

    public function testReportingWithoutAuth(): void
    {
        $postData = [
            'reportedID' => 1,
            'description' => 'This is a test report'
        ];

        $response = $this->json('POST', 'users/report', $postData);
        $response->seeStatusCode(401);
    }

    public function testReportingWithoutReportedID(): void
    {
        $user = $this->getTestUser();

        $postData = [
            'description' => 'This is a test report'
        ];

        $headers = $this->createAuthorizationHeaders($user);
        $response = $this->json('POST', 'users/report', $postData, $headers);
        $response->seeStatusCode(422);
    }

    public function testGettingAllUsersFailWithoutSuperAdmin(): void
    {
        $response = $this->json('GET', 'superadmin/users', []);
        $response->seeStatusCode(401);

        // Try with normal user
        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);
        $response = $this->json('GET', 'superadmin/users', [], $headers);
        $response->seeStatusCode(401);
    }

    public function testGettingAllUsers(): void
    {
        $user = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($user);
        $response = $this->json('GET', 'superadmin/users', [], $headers);
        $response->seeStatusCode(200);
    }

    public function testCreatingUserWithAdminRightsShouldFail(): void
    {

        $postData = [
            'firstName' => 'Bob',
            'lastName' => 'Swift',
            'email' => 'bob.test@gmail.com',
            'password' => 'test1234',
            'userType' => 'admin',
        ];

        $response = $this->json('POST', 'users', $postData);
        $response->seeStatusCode(201);
        $response->dontSeeJson([
            'userType' => 'admin'
        ]);
    }

    public function testCreatingUserWithSuperadminRightsShouldFail(): void
    {

        $postData = [
            'firstName' => 'Bob',
            'lastName' => 'Swift',
            'email' => 'bob.test2@gmail.com',
            'password' => 'test1234',
            'userType' => 'superadmin',
        ];

        $response = $this->json('POST', 'users', $postData);
        $response->seeStatusCode(201);
        $response->dontSeeJson([
            'userType' => 'superadmin'
        ]);
    }

    public function testUpdatingUserTypeWithoutSuperadminRightsShouldFail(): void
    {
        $user = $this->getTestUser();

        $postData = [
            'userType' => 'admin',
        ];

        $headers = $this->createAuthorizationHeaders($user);

        $response = $this->json('PATCH', 'users', $postData, $headers);
        $response->seeStatusCode(200);
        $response->dontSeeJson([
            'userType' => 'admin'
        ]);
    }

    public function testUpdatingUserTypeWithSuperadminRights(): void
    {
        $user = $this->getSuperadminUser();

        $postData = [
            'userToUpdate' => 1,
            'userType' => 'admin',
        ];

        $headers = $this->createAuthorizationHeaders($user);

        $response = $this->json('PATCH', 'superadmin/users', $postData, $headers);
        $response->seeStatusCode(200);
        $response->seeJson([
            'userType' => 'admin'
        ]);
    }
}
