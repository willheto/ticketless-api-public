<?php

use App\Models\Advertisement;
use Tests\TestCase;

class AdvertisementsControllerTest extends TestCase
{

    public function testGetActiveAdvertisements(): void
    {
        Advertisement::factory()->create(['isActive' => true]);
        Advertisement::factory()->create(['isActive' => false]);
        $this->json('GET', 'advertisements/active')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'advertisements' => [
                    '*' => [
                        'advertisementID',
                        'advertiser',
                        'contentHtml',
                        'isActive',
                        'views',
                        'clicks',
                        'redirectUrl',
                        'type',
                        'location',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->seeJson([
                'isActive' => true
            ]);
    }

    public function testPostAdvertisementView(): void
    {
        $advertisement = Advertisement::find(1);
        $viewsBefore = $advertisement->views;

        $this->json('POST', 'advertisements/1/view')
            ->seeStatusCode(200)
            ->seeJson([
                'views' => $viewsBefore + 1
            ]);
    }

    public function testPostAdvertisementClick(): void
    {
        $advertisement = Advertisement::find(2);
        $clicksBefore = $advertisement->clicks;

        $this->json('POST', 'advertisements/2/click')
            ->seeStatusCode(200)
            ->seeJson([
                'clicks' => $clicksBefore + 1
            ]);
    }

    public function testGetAllAdvertisementsAsUserShouldFail(): void
    {
        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);

        $this->json('GET', 'superadmin/advertisements', [], $headers)
            ->seeStatusCode(401);
    }

    public function testGetAllAdvertisementsAsAdminShouldFail(): void
    {
        $user = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($user);

        $this->json('GET', 'superadmin/advertisements', [], $headers)
            ->seeStatusCode(401);
    }

    public function testGetAllAdvertisements(): void
    {
        $user = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($user);

        $this->json('GET', 'superadmin/advertisements', [], $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'advertisements' => [
                    '*' => [
                        'advertisementID',
                        'advertiser',
                        'contentHtml',
                        'isActive',
                        'views',
                        'clicks',
                        'redirectUrl',
                        'type',
                        'location',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function testCreateAdvertisementAsUserShouldFail(): void
    {
        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertiser' => 'Test Advertiser',
            'contentHtml' => 'Test Content',
            'isActive' => true,
            'redirectUrl' => 'https://example.com',
            'type' => 'global',
        ];

        $this->json('POST', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testCreateAdvertisementAsAdminShouldFail(): void
    {
        $user = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertiser' => 'Test Advertiser',
            'contentHtml' => 'Test Content',
            'isActive' => true,
            'redirectUrl' => 'https://example.com',
            'type' => 'global',
        ];

        $this->json('POST', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testCreateAdvertisement(): void
    {
        $user = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertiser' => 'Test Advertiser',
            'contentHtml' => 'Test Content',
            'isActive' => true,
            'redirectUrl' => 'https://example.com',
            'type' => 'global',
        ];

        $this->json('POST', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(201)
            ->seeJson([
                'advertiser' => 'Test Advertiser',
                'contentHtml' => 'Test Content',
                'isActive' => true,
                'redirectUrl' => 'https://example.com',
                'type' => 'global',
            ]);
    }

    public function testUpdateAdvertisementAsUserShouldFail(): void
    {
        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertisementID' => 1,
            'advertiser' => 'Test Advertiser changed',
        ];

        $this->json('PATCH', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testUpdateAdvertisementAsAdminShouldFail(): void
    {
        $user = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertisementID' => 1,
            'advertiser' => 'Test Advertiser changed',
        ];

        $this->json('PATCH', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testUpdateAdvertisement(): void
    {
        $user = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertisementID' => 1,
            'advertiser' => 'Test Advertiser changed',
        ];

        $this->json('PATCH', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'advertiser' => 'Test Advertiser changed'
            ]);
    }

    public function testDeleteAdvertisementAsUserShouldFail(): void
    {
        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertisementID' => 1,
        ];

        $this->json('DELETE', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testDeleteAdvertisementAsAdminShouldFail(): void
    {
        $user = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertisementID' => 1,
        ];

        $this->json('DELETE', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testDeleteAdvertisement(): void
    {
        $user = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'advertisementID' => 1,
        ];

        $this->json('DELETE', 'superadmin/advertisements', $postData, $headers)
            ->seeStatusCode(200);
    }
}
