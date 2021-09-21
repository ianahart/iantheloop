<?php

namespace Database\Factories;

use App\Models\FollowSuggestion;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;


class FollowSuggestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FollowSuggestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'profile_id' => Profile::factory(),
            'prospect_user_id' => User::factory(),
            'rejected' => 0,
            'suggest' => 1,
            'pending' => 0,

        ];
    }
}
