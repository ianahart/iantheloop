<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;

use Tests\TestCase;
use App\Models\User;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_out_a_user_and_destroys_token()
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;


        $response = $this
            ->withHeaders(
                [
                    'Authorization' => 'Bearer ' . $token,
                ]
            )
            ->postJson('/api/auth/logout');


        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                'logout' => true,
            ]
        );
    }
}
