<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use App\Managers\AuthManager;
use App\Models\Organization;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected static $dbInitialized = false;
    protected static $dbSeeded = false;
    protected static $clearDb = false;
    protected static $testUser;
    protected static $testFilesHash;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        putenv('APP_ENV=testing');
        return require __DIR__ . '/../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (env('APP_ENV') !== 'testing') {
            exit('Wrong environment, should be testing. Current environment is ' . env('APP_ENV'));
        }

        if (!self::$dbInitialized) {
            self::$dbInitialized = true;
            $this->initDatabase();
        }

        if ($this->shouldReSeedDatabase()) {
            self::$dbSeeded = false;
        }

        if (!self::$dbSeeded) {
            self::$dbSeeded = true;
            $this->seedDatabase();
        }
    }

    private function shouldReSeedDatabase(): bool
    {
        $currentHash = $this->getTestFilesHash();
        if (self::$testFilesHash !== $currentHash) {
            self::$testFilesHash = $currentHash;
            return true;
        }
        return false;
    }

    private function getTestFilesHash(): string
    {
        $files = File::allFiles(__DIR__);
        $filesContent = '';
        foreach ($files as $file) {
            $filesContent .= File::get($file);
        }
        return md5($filesContent);
    }

    protected static function getTestUser(): User
    {
        return  User::where('userType', 'user')->first();
    }

    protected static function getAdminUser(): User
    {
        return  User::where('userType', 'admin')->first();
    }

    protected static function getSuperadminUser(): User
    {
        return  User::where('userType', 'superadmin')->first();
    }

    protected function createAuthorizationHeaders(User $user): array
    {
        $authManager = new AuthManager();
        $jwt = $authManager->createUserJwt($user->userID);
        $headers = ['Authorization' => 'Bearer ' . $jwt];
        return $headers;
    }

    private static function initDatabase(): void
    {
        Artisan::call('migrate:fresh');
    }

    private static function seedDatabase(): void
    {
        Organization::factory()->count(5)->create();

        $testUser = [
            'organizationID' => Organization::all()->random()->organizationID,
            'firstName' => 'Less',
            'lastName' => 'Ticket',
            'email' => 'less.ticket@ticketless.fi',
            'password' => password_hash('test', PASSWORD_BCRYPT)
        ];

        $adminUser = [
            'organizationID' => Organization::all()->random()->organizationID,
            'firstName' => 'Admin',
            'lastName' => 'User',
            'email' => 'admin.ticket@ticketless.fi',
            'password' => password_hash('test', PASSWORD_BCRYPT),
            'userType' => 'admin'
        ];

        $superadminUser = [
            'organizationID' => Organization::all()->random()->organizationID,
            'firstName' => 'Super',
            'lastName' => 'Admin',
            'email' => 'henri.willman@ticketless.fi',
            'password' => password_hash('test', PASSWORD_BCRYPT),
            'userType' => 'superadmin'
        ];

        User::create($testUser);
        User::create($adminUser);
        User::create($superadminUser);
    }
}
