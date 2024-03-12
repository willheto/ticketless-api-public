<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\File;

class FilesSeeder extends Seeder
{
    /**
     * Run the tickets seeder.
     *
     * @return void
     */
    public function run()
    {
        File::factory()->count(1)->create();
    }
}
