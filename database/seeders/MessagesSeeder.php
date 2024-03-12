<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessagesSeeder extends Seeder
{
    /**
     * Run the messages seeder.
     *
     * @return void
     */
    public function run()
    {
        Message::factory()->count(200)->create();
    }
}
