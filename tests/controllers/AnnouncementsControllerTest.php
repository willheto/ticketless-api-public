<?php

use App\Models\Announcement;
use Tests\TestCase;

class AnnouncementsControllerTest extends TestCase
{
    public function testGetAllAnnouncementsAsUserShouldFail(): void
    {
        Announcement::factory()->count(5)->create();

        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);

        $this->json('GET', 'superadmin/announcements', [], $headers)
            ->seeStatusCode(401);
    }

    public function testGetAllAnnouncementsAsAdminShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $this->json('GET', 'superadmin/announcements', [], $headers)
            ->seeStatusCode(401);
    }

    public function testGetAllAnnouncementsAsSuperadmin(): void
    {
        $superadmin = $this->getSuperadminUser();

        $headers = $this->createAuthorizationHeaders($superadmin);

        $this->json('GET', 'announcements', [], $headers)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'announcements' => [
                    '*' => [
                        'announcementID',
                        'title',
                        'content',
                        'isActive',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }



    public function testGetActiveAnnouncements(): void
    {
        Announcement::factory()->create(['isActive' => true]);
        Announcement::factory()->create(['isActive' => false]);

        $this->json('GET', 'announcements')
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'announcements' => [
                    '*' => [
                        'announcementID',
                        'title',
                        'content',
                        'isActive',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->seeJson([
                'isActive' => true
            ]);
    }

    public function testCreateAnnouncement(): void
    {
        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);

        $postData = [
            'title' => 'Test announcement',
            'content' => 'This is a test announcement'
        ];

        $this->json('POST', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(201)
            ->seeJson([
                'title' => 'Test announcement'
            ]);
    }

    public function testCreateAnnouncementWithActiveDefinedAsTrue(): void
    {
        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);

        $postData = [
            'title' => 'Test announcement',
            'content' => 'This is a test announcement',
            'isActive' => true
        ];

        $this->json('POST', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(201)
            ->seeJson([
                'title' => 'Test announcement',
                'isActive' => true
            ]);
    }

    public function testCreateAnnouncementWithActiveDefinedAsFalse(): void
    {
        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);

        $postData = [
            'title' => 'Test announcement',
            'content' => 'This is a test announcement',
            'isActive' => false
        ];

        $this->json('POST', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(201)
            ->seeJson([
                'title' => 'Test announcement',
                'isActive' => false
            ]);
    }

    public function testCreateAnnouncementAsUserShouldFail(): void
    {
        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);

        $postData = [
            'title' => 'Test announcement',
            'content' => 'This is a test announcement'
        ];

        $this->json('POST', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testCreateAnnouncementAsAdminShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $postData = [
            'title' => 'Test announcement',
            'content' => 'This is a test announcement'
        ];

        $this->json('POST', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testUpdateAnnouncement(): void
    {
        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);

        $announcement = Announcement::factory()->create();

        $postData = [
            'announcementID' => $announcement->announcementID,
            'title' => 'Updated title',
            'content' => 'Updated content'
        ];

        $this->json('PATCH', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(200)
            ->seeJson([
                'title' => 'Updated title'
            ]);
    }

    public function testUpdateAnnouncementAsUserShouldFail(): void
    {
        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);

        $announcement = Announcement::factory()->create();

        $postData = [
            'announcementID' => $announcement->announcementID,
            'title' => 'Updated title',
            'content' => 'Updated content'
        ];

        $this->json('PATCH', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testUpdateAnnouncementAsAdminShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $announcement = Announcement::factory()->create();

        $postData = [
            'announcementID' => $announcement->announcementID,
            'title' => 'Updated title',
            'content' => 'Updated content'
        ];

        $this->json('PATCH', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testDeleteAnnouncement(): void
    {
        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);

        $announcement = Announcement::factory()->create();

        $postData = [
            'announcementID' => $announcement->announcementID
        ];

        $this->json('DELETE', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(200);
    }

    public function testDeleteAnnouncementAsUserShouldFail(): void
    {
        $user = $this->getTestUser();
        $headers = $this->createAuthorizationHeaders($user);

        $announcement = Announcement::factory()->create();

        $postData = [
            'announcementID' => $announcement->announcementID
        ];

        $this->json('DELETE', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(401);
    }

    public function testDeleteAnnouncementAsAdminShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $announcement = Announcement::factory()->create();

        $postData = [
            'announcementID' => $announcement->announcementID
        ];

        $this->json('DELETE', 'superadmin/announcements', $postData, $headers)
            ->seeStatusCode(401);
    }
}
