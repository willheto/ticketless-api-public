<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MongoDBEventsSeeder extends Seeder
{
    /**
     * Run the events seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->importOldEvents();
    }

    protected function importOldEvents()
    {
        $jsonPath = database_path('seeders/eventless-production.events.json'); 

        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data as $record) {
            DB::table('events')->insert([
                'name' => $record['name'],
                'location' => $record['location'],
                'type' => $record['type'],
                'date' => $record['date'],
                'trendingScore' => $record['trendingScore'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')

            ]);
        }
    }
}
