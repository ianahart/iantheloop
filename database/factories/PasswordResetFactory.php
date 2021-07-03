<?php

namespace Database\Factories;

use App\Models\PasswordReset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;

class PasswordResetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PasswordReset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {


        $data = [
            'user_id' => $this->faker->randomNumber(9),
            'exp' => time() + 86400,
            'iat' => time(),
            'nbf' => time(),
        ];
        $customClaims = JWTFactory::customClaims($data);
        $payload = JWTFactory::make($data);
        $token = JWTAuth::encode($payload);


        return [
            'email' => $this->faker->unique()->email,
            'token' => $token,
        ];
    }
}
