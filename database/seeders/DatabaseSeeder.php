<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            OrganizationsSeeder::class,
            UsersSeeder::class,
            EventsSeeder::class,
            TicketsSeeder::class,
            AdvertisementsSeeder::class,
            ChatsSeeder::class,
            MessagesSeeder::class,
            AnnouncementsSeeder::class,
            FilesSeeder::class,
        ]);
    }
}
