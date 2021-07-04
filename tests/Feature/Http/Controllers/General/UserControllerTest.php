<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public $token;
    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->create(
                ['status' => 'offline', 'id' => 40]
            );
        $this->token = JWTAuth::fromUser($this->user);
    }



    /** @test */
    public function it_updates_the_users_status()
    {

        $response = $this
            ->withHeaders(
                [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token
                ]
            )
            ->patchJson(
                '/api/auth/user/status/' . $this->user->id . '/update',
                ['status' => 'online']
            );

        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                'new_user_status' => 'online',
                'status_updated' => true
            ]
        );
        $this->user->refresh();
        $this->assertEquals($this->user->status, $response->getData()->new_user_status);
    }

    /** @test */
    public function it_only_allows_current_user_to_update_status()
    {

        $response = $this
            ->withHeaders(
                [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token
                ]
            )
            ->patchJson(
                '/api/auth/user/status/39/update',
                ['status' => 'online']
            );

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'error' => 'User is forbidden from making this request',
                'status_updated' => false,
            ]
        );
    }
}
