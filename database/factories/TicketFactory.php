<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;
use App\Models\Event;
use App\Models\User;

class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create('fi_FI');

        $userID = User::all()->random()->userID;
        $eventID = Event::all()->random()->eventID;
        $header = $faker->word();
        $description = $faker->sentence();
        $price = $faker->randomFloat(2, 5, 50);
        $quantity = $faker->numberBetween(1, 5);
        $requiresMembership = false;
        $isSelling = $faker->boolean(50);

        return [
            'userID' => $userID,
            'eventID' => $eventID,
            'header' => $header,
            'description' => $description,
            'price' => $price,
            'quantity' => $quantity,
            'requiresMembership' => $requiresMembership,
            'isSelling' => $isSelling,
        ];
    }
}
