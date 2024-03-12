<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventsSeeder extends Seeder
{
    /**
     * Run the tickets seeder.
     *
     * @return void
     */
    public function run()
    {
        Event::factory()->count(20)->create();
    }
}
