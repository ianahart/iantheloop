<?php

namespace Database\Factories;

use App\Models\Stat;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;


class StatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'profile_id' => Profile::factory(),
            'user_id' => User::factory(),
            'following' => null,
            'followers' => null,
            'following_count' => 0,
            'followers_count' => 0,
            'notifications' => null,
            'name' => '',
        ];
    }
}
