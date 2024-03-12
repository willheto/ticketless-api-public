<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;
use App\Providers\KummeliProvider;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker::create('fi_FI');
        $faker->addProvider(new KummeliProvider($faker));

        $chat = Chat::inRandomOrder()->first();

        // randomize sender and receiver


        $chatID = $chat->chatID;
        $senderID = $chat->user1ID;
        $receiverID = $chat->user2ID;
        if ($faker->boolean(50)) {
            $senderID = $chat->user2ID;
            $receiverID = $chat->user1ID;
        }
        $content = $faker->kummeliSentence();
        $isRead = true;

        return [
            'chatID' => $chatID,
            'senderID' => $senderID,
            'receiverID' => $receiverID,
            'content' => $content,
            'isRead' => $isRead,
        ];
    }
}
