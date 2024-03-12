<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;

class TicketsSeeder extends Seeder
{
    /**
     * Run the tickets seeder.
     *
     * @return void
     */
    public function run()
    {
        Ticket::factory()->count(150)->create();
    }
}
