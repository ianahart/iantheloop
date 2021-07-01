<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tymon\JWTAuth\Facades\JWTAuth;

use Tests\TestCase;
use App\Models\User;


class refreshTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_refreshes_a_token()
    {
        $user = User::factory()->create();









        $token = JWTAuth::fromUser($user);


        $response = $this
            ->withHeaders(
                [
                    'HTTP_Authorization' => 'Bearer ' . $token
                ]
            )
            ->postJson('/api/auth/token/refresh');


        $response->dumpHeaders();

        $response->dump();
    }
}
