<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;


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
        return [
            'sender_user_id' => User::factory(),
            'recipient_user_id' => User::factory(),
            'conversation_id' => Conversation::factory(),
            'sender_name' => '',
            'recipient_name' => '',
            'message' => $this->faker->text(30),
            'message_sent' => '',
        ];
    }
}
