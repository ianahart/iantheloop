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

    /**
     * Indicate that the user is suspended.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function fullMedia()
    {
        return $this->state(function (array $attributes) {
            return [
                'background_picture' => 'https://hart-looped.s3.amazonaws.com/60a6c999541f0test_image.jpg',
                'background_filename' => 'test_image.jpg',
                'profile_picture' => 'https://hart-looped.s3.amazonaws.com/60a6c999541f0test_image_md.png',
                'profile_filename' => 'test_image_md.png',
            ];
        });
    }


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
            'profile_filename' => null,
            'background_picture' => null,
            'background_filename' => null,
            'work_currently' => '',
        ];
    }
}
