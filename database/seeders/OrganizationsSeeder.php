<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationsSeeder extends Seeder
{
    /**
     * Run the organizations seeder.
     *
     * @return void
     */
    public function run()
    {
        $ticketlessOrganization = new Organization();
        $ticketlessOrganization->name = 'Ticketless Oy';
        $ticketlessOrganization->license = 'pro';
        $ticketlessOrganization->location = 'Jyväskylä';

        $ticketlessOrganization->save();
        Organization::factory()->count(5)->create();
    }
}
