<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class OrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create('fi_FI');

        $name = $faker->company;
        $license = $faker->randomElement(['free', 'basic', 'pro']);
        $finnishTowns = FinnishTowns::getFinnishTowns();
        $location = $faker->randomElement($finnishTowns);

        return [
            'name' => $name,
            'license' => $license,
            'location' => $location
        ];
    }
}
