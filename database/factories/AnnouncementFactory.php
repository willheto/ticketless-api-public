<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class AnnouncementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create('fi_FI');


        $title = $faker->sentence(1);
        $content = $faker->paragraph(1);
        $isActive = $faker->boolean(70);

        return [
            'title' => $title,
            'content' => $content,
            'isActive' => $isActive,
        ];
    }
}
