<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;


use function PHPUnit\Framework\assertEquals;

class refreshTokenControllerTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_rejects_an_expired_token()
    {


        $user = User::factory()->has(Setting::factory())->create([]);

        $lookup = base64_encode(random_bytes(9));
        $storedNonce = base64_encode(random_bytes(18));


        $user->setting->lookup = $lookup;
        $user->setting->remember_me = 1;
        $user->setting->expire_in = 86400 * 30;

        $user->setting->save();
        $user->setting->refresh();

        $validator = hash_hmac('sha256', $user->setting->user->full_name, $user->setting->ip_address, $storedNonce);
        $user->setting->validator = $validator;

        $user->setting->save();

        $token = $user->createToken('auth_token')->plainTextToken;



        $this->travel(3)->weeks();

        $response = $this
            ->postJson(
                '/api/auth/token/refresh',
                [
                    'token' => $token,
                ]
            );

        $response = $this
            ->postJson(
                '/api/auth/token/refresh',
                [
                    'token' => $token,
                ]
            );

        $response->assertStatus(403);
        assertEquals('Token Expired', $response->getData()->message);
    }
}
