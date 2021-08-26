<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    /**
     * Indicate that the user is has created a profile.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function userProfileCreated()
    {
        return $this
            ->state(function (array $attributes) {
                return [
                    'profile_created' => true,
                ];
            });
    }

    public function definition()
    {

        $fullName = $this->faker->name;
        $firstName = explode(' ', $fullName)[0];
        $lastName = explode(' ', $fullName)[1];

        return [
            'full_name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => $this->faker->password(10),
            'remember_token' => Str::random(10),
            'is_logged_in' => false,
            'profile_created' => false,
            'status' => '',
            'cur_chat_window_user_id' => '',
            'slug' => '',
            'last_following_user_id' => '',
            'last_following_suggestion_user_id' => '',

        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
