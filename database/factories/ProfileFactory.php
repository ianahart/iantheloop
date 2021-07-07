<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'gender' => 'female',
            'user_id' => User::factory(),
            'birth_day' => '',
            'birth_month' => '',
            'birth_year' => '',
            'display_name' => 'somename341',
            'town' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => 'United States',
            'phone' => '123-342-1234',
            'links' => json_encode([$this->faker->url, $this->faker->url]),
            'bio' => $this->faker->text(100),
            'relationship' => 'Married',
            'interests' => json_encode([
                array(
                    'id' => 1,
                    'name' => 'yoga'
                ),
                array(
                    'id' => 2,
                    'name' => 'night skiing'
                ),
                array(
                    'id' => 3,
                    'name' => 'exploring'
                ),
            ]),
            'company' => $this->faker->company(),
            'position' => $this->faker->jobTitle(),
            'work_city' => $this->faker->city(),
            'description' => $this->faker->text(100),
            'month_from' => '',
            'year_from' => '',
            'month_to' => '',
            'year_to' => '',
            'profile_picture' => null,
            'profile_filename' => '',
            'background_picture' => null,
            'background_filename' => '',
            'work_currently' => '',
        ];
    }
}
