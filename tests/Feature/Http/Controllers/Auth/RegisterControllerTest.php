<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Requests\RegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use JMac\Testing\Traits\AdditionalAssertions;


class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use AdditionalAssertions;



    /** @test */
    public function it_validates_and_registers_a_new_user()
    {
        $this->assertActionUsesFormRequest(RegisterController::class, 'store', RegisterRequest::class);

        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $email = $this->faker->email();
        $password = 'daisy?L9Ter';


        $response = $this->postJson(
            '/api/auth/register',
            [
                'formData' => [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'email' => $email,
                    'password' => $password,
                    'password_confirmation' => $password,
                ]
            ]
        );

        $response->assertStatus(201);
        $response->assertJsonFragment(['isSubmitted' => true]);

        $user = User::where('email', '=', $email)
            ->where('full_name', '=', strtolower($firstName) . ' ' . strtolower($lastName))
            ->first();

        $this->assertNotNull($user);
    }
}
