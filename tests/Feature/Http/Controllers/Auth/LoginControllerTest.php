<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Events\UserStatusChanged;
use Illuminate\Support\Facades\Hash;



class LoginControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected string $route = '/api/auth/login';

    /** @test */
    public function it_authenticates_a_user_and_returns_a_token()
    {
        Event::fake();

        User::factory()
            ->has(Setting::factory())
            ->count(3)
            ->create(
                [
                    'password' => Hash::make('password12345%')
                ]
            );
        $authenticateUser = User::find(1);

        $response = $this->postJson($this->route, [
            'form' => [
                'email' => $authenticateUser->email,
                'password' => 'password12345%',
            ]
        ]);

        $response->assertStatus(200);
        Event::assertDispatched(UserStatusChanged::class);

        $response->assertJsonStructure(
            [
                'formSubmitted',
                'userLoggedIn',
                "token" => [
                    'access_token',
                    'token_type',
                    'user_info',
                ]
            ]
        );

        $this->assertAuthenticated('sanctum');
    }

    /** @test */
    public function it_tries_to_authenticate_user_with_non_existent_email()
    {
        $user = User::factory()
            ->create(
                [
                    'password' => Hash::make('passworD12345%'),
                    'email' => 'betsy.mallop@gmail.com'
                ]
            );

        $response = $this->postJson(
            $this->route,
            [
                'form' =>
                [
                    'email' => 'betsy.ballop@gmail.com',
                    'password' => 'passworD12345%',
                ]
            ]

        );

        $response->assertStatus(400);
        $response->assertJsonFragment(
            [
                'errors' =>
                'Sorry, we couldn\'t find an account with that email.',
            ]
        );
    }

    /** @test */
    public function it_tries_to_authenticate_user_with_invalid_password()
    {
        $user = User::factory()->create(
            [
                'password' => Hash::make('passworD12345%'),
            ]
        );

        $response = $this->postJson(
            $this->route,
            [
                'form' => [
                    'email' => $user->email,
                    'password' => 'Password12345%',
                ]
            ]
        );

        $response->assertStatus(400);
        $response->assertJsonFragment(
            [
                'errors' => 'The provided credentials are invalid.'
            ]
        );
    }

    /** @test */
    public function it_locks_out_guest_for_too_many_login_attempts()
    {
        $user = User::factory()
            ->create(
                [
                    'email' => 'betsy.mallop@gmail.com',
                    'password' => Hash::make('passworD12345%'),
                ]
            );

        $loginAttempts = 0;

        $responses = collect([]);

        while ($loginAttempts <= 5) :
            $response = $this->postJson(
                $this->route,
                [
                    'form' => [
                        'email' => $user->email,
                        'password' => 'Password12345%'
                    ]
                ]
            );
            $loginAttempts++;

            $responses->push($response);
        endwhile;

        $finalLoginAttempt = $responses->pop();

        $this->assertEquals(400, $finalLoginAttempt->getStatusCode());

        $finalLoginAttempt = json_decode($finalLoginAttempt->getContent(), true);

        $this->assertEqualsIgnoringCase(
            'You have been locked out for 15 minutes for too many login attempts',
            $finalLoginAttempt['password']['errors']
        );
    }
}
