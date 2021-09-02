<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\JWTGuard;
use Tymon\JWTAuth\Claims\Expiration;
use Tymon\JWTAuth\Claims\IssuedAt;
use Tymon\JWTAuth\Claims\Issuer;
use Tymon\JWTAuth\Claims\JwtId;
use Tymon\JWTAuth\Claims\NotBefore;
use Tymon\JWTAuth\Claims\Subject;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use DateTime;

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
    public function it_disallows_a_non_authenticated_user_from_trying_refresh()
    {

        $date = new DateTime();
        $now = $date->getTimestamp();

        $customClaims = JWTFactory::customClaims(
            [
                'iss' => new Issuer('http://looped.com'),
                'exp' => new Expiration($now + 1),
                'nbf' => new NotBefore($now),
                'iat' => new IssuedAt($now),
                'jti' => new JwtId('bar'),
                'sub' => new Subject(12345),
            ]
        );

        $payload = JWTFactory::make($customClaims);
        $token = JWTAuth::encode($payload);

        $response = $this
            ->postJson(
                '/api/auth/token/refresh',
                [
                    'token' => $token->get(),
                ]
            );

        $response->assertStatus(400);

        $this->assertNull($response->getData()->data);
        $this->assertEquals('User not found', $response->getData()->message);
    }

    /** @test */
    public function it_rejects_an_expired_token()
    {
        JWTAuth::factory()->setTTL(1);
        JWTAuth::factory()->setRefreshTTL(0);

        $token = JWTAuth::fromUser($this->user);
        JWTAuth::setToken($token);



        $this->travel(3)->minutes();
        $response = $this
            ->postJson(
                '/api/auth/token/refresh',
                [
                    'token' => $token,
                ]
            );

        $refreshedToken = json_decode(
            $response
                ->getData()
                ->access_token,
            true
        )['access_token'];

        $this->travel(2)->weeks();
        $response = $this
            ->postJson(
                '/api/auth/token/refresh',
                [
                    'token' => $refreshedToken,
                ]
            );

        $response->assertStatus(403);
        assertEquals('Token Expired', $response->getData()->message);
    }

    /** @test */
    public function it_refreshes_a_token()
    {

        $date = new DateTime();
        $now = $date->getTimestamp();

        $customClaims = JWTFactory::customClaims(
            [
                'iss' => new Issuer('http://example.com'),
                'exp' => new Expiration($now + 50),
                'nbf' => new NotBefore($now),
                'iat' => new IssuedAt($now),
                'jti' => new JwtId('foo'),
                'sub' => new Subject($this->user->id),
            ]
        );

        $payload = JWTFactory::make($customClaims);
        $token = JWTAuth::encode($payload);



        $this->travel(1)->minutes();

        $response = $this
            ->postJson(
                '/api/auth/token/refresh',
                [
                    'token' => $token->get(),
                ]
            );

        $this->travelBack();
        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'message']);

        list('access_token' => $accessToken, 'message' => $message) = json_decode(
            json_encode($response->getData()),
            true
        );

        $this->assertSame('Token refreshed', $message);

        $this->assertIsString(json_decode($accessToken, true)['access_token']);
        $this->assertNotEquals($token, json_decode($accessToken, true)['access_token']);
    }
}
