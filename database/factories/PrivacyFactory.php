<?php

namespace Database\Factories;

use App\Models\Privacy;
use App\Models\Setting;
use App\Models\User;
use App\Models\Stat;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrivacyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Privacy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'setting_id' => Setting::factory(),
            'blocked_user_id' => User::factory(),
            'blocked_by_user_id' => User::factory(),
            'profile_id' => Profile::factory(),
            'stat_id' => Stat::factory(),
            'blocked_profile' => 0,
            'blocked_messages' => 0,
            'blocked_stories' => 0,
            'blocked_profile_duration' => NULL,
            'blocked_messages_duration' => NULL,
            'blocked_stories_duration' => NULL,
            'created_in_unix' => NULL,

        ];
    }
}
