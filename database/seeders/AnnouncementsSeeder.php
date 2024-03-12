<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementsSeeder extends Seeder
{
    /**
     * Run the chats seeder.
     *
     * @return void
     */
    public function run()
    {
        Announcement::factory()->count(2)->create();
    }
}
