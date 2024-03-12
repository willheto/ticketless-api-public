<?php

use Tests\TestCase;
use App\Models\File;

class FilesControllerTest extends TestCase
{
    public function testCreateFileAsUserShouldFail(): void
    {
        $this->json(
            'POST',
            'superadmin/files',
            [
                'fileBase64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII',
                'fileName' => 'testFile',
            ]
        )->seeStatusCode(401);
    }

    public function testGetSingleFileAsUserShouldFail(): void
    {
        $this->json('GET', 'superadmin/files/1')->seeStatusCode(401);
    }

    public function testGetAllFilesAsUserShouldFail(): void
    {
        $this->json('GET', 'superadmin/files')
            ->seeStatusCode(401);
    }

    public function testDeleteFileAsUserShouldFail(): void
    {
        $this->json('DELETE', 'superadmin/files')
            ->seeStatusCode(401);
    }

    public function testUpdateFileAsUserShouldFail(): void
    {
        $this->json(
            'PATCH',
            'superadmin/files',
            [
                'fileBase64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII',
                'fileName' => 'testFile',
            ]
        )->seeStatusCode(401);
    }

    public function testCreateFileAsAdminShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $this->json(
            'POST',
            'superadmin/files',
            [
                'fileBase64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII',
                'fileName' => 'testFile',
            ],
            $headers
        )->seeStatusCode(401);
    }

    public function testGetSingleFileAsAdminShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $this->json(
            'GET',
            'superadmin/files/1',
            [],
            $headers
        )->seeStatusCode(401);
    }

    public function testGetAllFilesAsAdminnShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $this->json('GET', 'superadmin/files', [], $headers)
            ->seeStatusCode(401);
    }

    public function testDeleteFileAsAdminShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $this->json('DELETE', 'superadmin/files', [], $headers)
            ->seeStatusCode(401);
    }

    public function testUpdateFileAsAdminShouldFail(): void
    {
        $admin = $this->getAdminUser();
        $headers = $this->createAuthorizationHeaders($admin);

        $this->json(
            'PATCH',
            'superadmin/files',
            [
                'fileBase64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII',
                'fileName' => 'testFile',
            ],
            $headers
        )->seeStatusCode(401);
    }

    public function testCreateFile(): void
    {
        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);

        $this->json(
            'POST',
            'superadmin/files',
            [
                'fileBase64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII',
                'fileName' => 'testFile',
            ],
            $headers
        )->seeStatusCode(201);
    }

    public function testGetSingleFile(): void
    {

        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);
        $this->json(
            'GET',
            'superadmin/files/1',
            [],
            $headers
        )->seeStatusCode(200);
    }

    public function testGetAllFiles(): void
    {

        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);
        $this->json(
            'GET',
            'superadmin/files',
            [],
            $headers
        )
            ->seeStatusCode(200);
    }

    public function testDeleteFile(): void
    {

        $file = File::factory()->create();

        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);
        $this->json(
            'DELETE',
            'superadmin/files',
            ['fileID' => $file->fileID],
            $headers
        )
            ->seeStatusCode(200);
    }

    public function testUpdateFile(): void
    {
        $superadmin = $this->getSuperadminUser();
        $headers = $this->createAuthorizationHeaders($superadmin);
        $this->json(
            'PATCH',
            'superadmin/files',
            [
                'fileID' => 1,
                'fileBase64' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII',
                'fileName' => 'testFile',
            ],
            $headers
        )->seeStatusCode(200);
    }
}
