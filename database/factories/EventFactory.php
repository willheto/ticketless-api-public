<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create('fi_FI');

        $name = $faker->word();

        $finnishTowns = FinnishTowns::getFinnishTowns();
        $location = $faker->randomElement($finnishTowns);
        $type = 'party';
        $date = $faker->dateTimeBetween('now', '+1 year');
        $organizationID = Organization::all()->random()->organizationID;
        $status = $faker->randomElement(['active', 'inactive', 'redirect', 'scheduled']);
        $activeFrom = $faker->dateTimeBetween('-2 month', 'now');
        $activeTo = $faker->dateTimeBetween('now', '+2 month');
        $isPublic = $faker->boolean(80);
        $ticketSaleUrl = $faker->url;
        $showEventOnCalendar = $faker->boolean(80);
        
        $includeOrganization = $faker->boolean(50);
        $includeTicketSaleUrl = $status === 'redirect';
        $includeStartAndEndDates = $status === 'scheduled';

        return [
            'name' => $name,
            'location' => $location,
            'type' => $type,
            'date' => $date,
            'organizationID' => $includeOrganization ? $organizationID : null,
            'status' => $status,
            'activeFrom' => $includeStartAndEndDates ? $activeFrom : null,
            'activeTo' => $includeStartAndEndDates ? $activeTo : null,
            'isPublic' => $isPublic,
            'ticketSaleUrl' => $includeTicketSaleUrl ? $ticketSaleUrl : null,
            'showEventOnCalendar' => $showEventOnCalendar,
        ];
    }
}
