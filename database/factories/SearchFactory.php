<?php

namespace Database\Factories;

use App\Models\Search;
use App\Models\User;
use app\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class SearchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Search::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'searcher_user_id' => User::factory(),
            'searched_user_id' => User::factory(),
            'profile_id' => Profile::factory(),
            'created_in_unix' => now()->timestamp,
            'purge_in_unix' => now()->timestamp + 86400 * 90,
        ];
    }
}
