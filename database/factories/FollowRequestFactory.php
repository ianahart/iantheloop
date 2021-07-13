<?php

namespace Database\Factories;

use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FollowRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'requester_user_id' => User::factory(),
            'receiver_user_id' => User::factory(),
            'request_date_sent' => $this->faker->date('g:ia,M jS Y'),
        ];
    }
}
