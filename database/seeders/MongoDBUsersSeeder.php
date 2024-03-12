<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MongoDBUsersSeeder extends Seeder
{
    /**
     * Run the users seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->importOldUsers();
    }

    protected function importOldUsers()
    {
        $jsonPath = database_path('seeders/ticketless-production.users.json'); 

        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data as $record) {
            DB::table('users')->insert([
                'email' => $record['email'],
                'firstName' => $record['firstName'],
                'lastName' => $record['lastName'],
                'password' => $record['passwordHash'],
                'phoneNumber' => $record['phoneNumber'],
                'city' => $record['city'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
