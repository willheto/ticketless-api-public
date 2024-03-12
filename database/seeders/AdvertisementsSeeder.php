<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use Illuminate\Database\Seeder;

class AdvertisementsSeeder extends Seeder
{
    /**
     * Run the advertisements seeder.
     *
     * @return void
     */
    public function run()
    {
        Advertisement::factory()->count(3)->create();
    }
}
