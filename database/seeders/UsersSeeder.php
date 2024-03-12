<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the users seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->createAdminUsers();
        User::factory()->count(10)->create();
    }

    protected function createAdminUsers()
    {
        $ticketlessAdminUsers = [
            [
                'organizationID' => Organization::where('name', 'Ticketless Oy')->first()->organizationID,
                'firstName' => 'Henri',
                'lastName' => 'Willman',
                'email' => 'henri.willman@ticketless.fi',
                'userType' => 'superadmin',
                'password' => password_hash('test', PASSWORD_BCRYPT)
            ],
            [
                'organizationID' => Organization::where('name', 'Ticketless Oy')->first()->organizationID,
                'firstName' => 'Otto',
                'lastName' => 'Örn',
                'email' => 'otto.orn@ticketless.fi',
                'userType' => 'superadmin',
                'password' => password_hash('test', PASSWORD_BCRYPT)
            ],
            [
                'organizationID' => Organization::where('name', 'Ticketless Oy')->first()->organizationID,
                'firstName' => 'Santeri',
                'lastName' => 'Pohjakallio',
                'email' => 'santeri.pohjakallio@ticketless.fi',
                'userType' => 'superadmin',
                'password' => password_hash('test', PASSWORD_BCRYPT)
            ],
            [
                'organizationID' => Organization::where('name', 'Ticketless Oy')->first()->organizationID,
                'firstName' => 'Miska',
                'lastName' => 'Lampinen',
                'email' => 'miska.lampinen@ticketless.fi',
                'userType' => 'superadmin',
                'password' => password_hash('test', PASSWORD_BCRYPT)
            ],
            [
                'organizationID' => Organization::where('name', 'Ticketless Oy')->first()->organizationID,
                'firstName' => 'Anna',
                'lastName' => 'Luodemäki',
                'email' => 'anna.luodemäki@ticketless.fi',
                'userType' => 'superadmin',
                'password' => password_hash('test', PASSWORD_BCRYPT)
            ]
        ];

        foreach ($ticketlessAdminUsers as $ticketlessAdminUser) {
            User::create($ticketlessAdminUser);
        }
    }
}
