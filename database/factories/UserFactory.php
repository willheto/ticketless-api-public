<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create('fi_FI');
        $finnishTowns = FinnishTowns::getFinnishTowns();

        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $email = iconv('UTF-8', 'ASCII//TRANSLIT', $firstName . '.' . $lastName . rand(0, 99) . '@ticketless.fi');
        $phoneNumber = $faker->phoneNumber;
        $city = $faker->randomElement($finnishTowns);
        $userType = $faker->randomElement(['user', 'admin']);
        $password = password_hash('test', PASSWORD_BCRYPT);
        $language = $faker->randomElement(['fi', 'en']);
        $organizationID = Organization::all()->random()->organizationID;


        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'city' => $city,
            'userType' => $userType,
            'password' => $password,
            'language' => $language,
            'organizationID' => $userType === 'admin' ? $organizationID : null,
        ];
    }
}
