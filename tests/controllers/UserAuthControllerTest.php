<?php

namespace Tests\Controllers;

use Tests\TestCase;

class UserAuthControllerTest extends TestCase
{

    public function testSuccessfulLogin(): void
    {

        $email = 'less.ticket@ticketless.fi';
        $password = 'test';

        $postData = ['email' => $email, 'password' => $password];

        $this->json('POST', 'user/login', $postData)
            ->seeStatusCode(200);
    }

    public function testWrongPostDataLogin(): void
    {
        $this->json('POST', 'user/login', [])->seeStatusCode(422);

        $this->json('POST', 'user/login', [
            'email' => 'test'
        ])->seeStatusCode(422);

        $this->json('POST', 'user/login', [
            'password' => 'test'
        ])->seeStatusCode(422);

        $this->json('POST', 'user/login', [
            'wrongKey' => 'test'
        ])->seeStatusCode(422);
    }

    public function testForgotPassword(): void
    {
        $testUser = $this->getTestUser();
        $email = $testUser->email;

        $this->json('POST', 'users/forgot-password', ['email' => $email])
            ->seeStatusCode(200);

        $this->json('POST', 'users/forgot-password', ['email' => 'test'])
            ->seeStatusCode(404);
    }

    public function testCheckCode(): void
    {
        $testUser = $this->getTestUser();
        $email = $testUser->email;

        $this->json('POST', 'users/forgot-password', ['email' => $email]);

        $passwordCode = $this->getTestUser()->passwordCode;

        $this->json('POST', 'users/check-code', ['code' => $passwordCode])
            ->seeStatusCode(200);

        $this->json('POST', 'users/check-code', ['code' => 'test'])
            ->seeStatusCode(400);
    }
}
