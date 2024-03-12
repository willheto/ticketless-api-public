<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class ChatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Chat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create('fi_FI');

        $usersInRandomOrder = User::inRandomOrder()->get();
        $user1ID = $usersInRandomOrder->first()->userID;
        $user2ID = $usersInRandomOrder->last()->userID;
        $ticketID = Ticket::inRandomOrder()->first()->ticketID;
        $isActive = $faker->boolean(70);

        return [
            'user1ID' => $user1ID,
            'user2ID' => $user2ID,
            'ticketID' => $ticketID,
            'isActive' => $isActive,
        ];
    }
}
