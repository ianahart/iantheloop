<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'remember_me' => 0,
            'user_id' => User::factory(),
            'block_profile_on' => 0,
            'block_messages_on' => 0,
            'block_stories_on' => 0,
            'validator' => NULL,
            'ip_address' => $this->faker->ipv6(),
            'lookup' => NULL,
            'user_agent' => $this->faker->userAgent(),
            'expire_in' => NULL,
            'password_updated' => 0,
            'password_updated_on' => NULL,
        ];
    }
}
