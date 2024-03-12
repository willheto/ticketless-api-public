<?php

namespace Database\Seeders;

use App\Models\Chat;
use Illuminate\Database\Seeder;

class ChatsSeeder extends Seeder
{
    /**
     * Run the chats seeder.
     *
     * @return void
     */
    public function run()
    {
        Chat::factory()->count(20)->create();
    }
}
