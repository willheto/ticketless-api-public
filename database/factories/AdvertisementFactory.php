<?php

namespace Database\Factories;

use App\Models\Advertisement;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class AdvertisementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Advertisement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create('fi_FI');


        $finnishTowns = FinnishTowns::getFinnishTowns();
        $advertiser = $faker->company;
        $isActive = $faker->boolean(70);
        $views = $faker->numberBetween(0, 1000);
        $clicks = $faker->numberBetween(0, 1000);
        $redirectUrl = $faker->url;
        $type = $faker->randomElement(['local', 'global', 'toast']);
        $advertisementImage = $type !== 'toast' ? $faker->imageUrl(640, 480, 'advertisement', true, $advertiser) : null;
        $contentHtml =  $type !== 'toast' ? "<img src='$advertisementImage' alt='$advertiser' />" : $faker->sentence();

        $location = $type === 'local' ? $faker->randomElement($finnishTowns) : null;


        return [
            'advertiser' => $advertiser,
            'contentHtml' => $contentHtml,
            'isActive' => $isActive,
            'views' => $views,
            'clicks' => $clicks,
            'redirectUrl' => $redirectUrl,
            'type' => $type,
            'location' => $location,
        ];
    }
}
