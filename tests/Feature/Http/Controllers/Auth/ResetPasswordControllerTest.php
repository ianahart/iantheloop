<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use DateTime;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_does_not_reset_password_if_validators_not_passed()
    {
        $passwords = ['', 'apple1@3D', 'sds2', 'sdeprl4!@d'];

        $statusCodes = [];
        foreach ($passwords as $key => $password) {
            $response = $this
                ->postJson(
                    '/api/auth/reset-password/',
                    [
                        'formData' => [

                            'password' => $password,
                            'password_confirmation' => $key === count($passwords) - 1 ? 'sdeprl4!@d' : 'apple5@s',
                        ],
                        'formName' => 'resetForm',
                        'resetToken' => '',
                    ]
                );
            array_push($statusCodes, $response->getStatusCode());
        }

        $this->assertCount(4, $statusCodes);
        $this->assertSame([422, 422, 422, 422], $statusCodes);
    }

    /** @test */
    public function it_fails_when_reset_token_is_invalid()
    {
        PasswordReset::factory()->create();

        $data = [
            'user_id' => 1,
            'exp' => time() + 86400,
            'iat' => time(),
            'nbf' => time(),
        ];
        $customClaims = JWTFactory::customClaims($data);
        $payload = JWTFactory::make($data);
        $token = JWTAuth::encode($payload);

        $password = 'Password1234$';
        $passwordConfirmation = 'Password1234$';

        $response = $this->postJson(
            '/api/auth/reset-password/',
            [
                'formData' => [
                    'password' => $password,
                    'password_confirmation' => $passwordConfirmation
                ],
                'formName' => 'resetForm',
                'resetToken' => $token->get(),
            ]
        );

        $response->assertStatus(404);
        $this->assertSame('Please try again. Something went wrong, click the link below.', $response->getData()->error);
    }


    /** @test */
    public function it_fails_if_the_reset_token_has_expired()
    {

        $data = [
            'user_id' => 1,
            'exp' => time() + 86400,
            'iat' => time(),
            'nbf' => time(),
        ];

        $customClaims = JWTFactory::customClaims($data);
        $payload = JWTFactory::make($data);
        $token = JWTAuth::encode($payload);

        $oneDayAgo = time() - 86420;
        $date = new DateTime();
        $date->setTimestamp($oneDayAgo);

        PasswordReset::factory()
            ->create(
                [
                    'token' => $token->get(),
                    'created_at' =>  $date->format('Y-m-d H:i:s'),
                ]
            );

        $password = 'Password1234$';
        $passwordConfirmation = 'Password1234$';

        $response = $this->postJson(
            '/api/auth/reset-password/',
            [
                'formData' => [
                    'password' => $password,
                    'password_confirmation' => $passwordConfirmation
                ],
                'formName' => 'resetForm',
                'resetToken' => $token->get(),
            ]
        );

        $response->assertStatus(404);
        $this->assertSame('The link has expired please return to forgot password page', $response->getData()->error);
    }

    /** @test */
    public function it_fails_if_the_password_is_same_as_old()
    {
        $oldPassword = 'Oldpassword123$';
        $user = User::factory()
            ->create(
                [
                    'id' => 1,
                    'email' => 'betsy.rippey@gmail.com',
                    'password' => Hash::make($oldPassword),
                ]
            );

        $data = [
            'user_id' => $user->id,
            'exp' => time() + 86400,
            'iat' => time(),
            'nbf' => time(),
        ];
        $customClaims = JWTFactory::customClaims($data);
        $payload = JWTFactory::make($data);

        $token = JWTAuth::encode($payload);

        $resetToken = PasswordReset::factory()
            ->create(
                [
                    'token' => $token->get(),
                ]
            );

        $response = $this->postJson(
            '/api/auth/reset-password/',
            [
                'formData' => [
                    'password' => $oldPassword,
                    'password_confirmation' => $oldPassword,
                ],
                'formName' => 'resetForm',
                'resetToken' => $resetToken->token,
            ]
        );
        $response->assertStatus(400);
        $this->assertSame('New password cannot be the same as old password', $response->getData()->error);
    }

    /** @test */
    public function it_resets_a_users_password()
    {

        $oldPassword = 'passworD1234$';
        $user = User::factory()
            ->create(
                [
                    'id' => 22,
                    'email' => 'betsy.rippey@gmail.com',
                    'password' => Hash::make($oldPassword),
                ]
            );

        $data = [
            'user_id' => $user->id,
            'exp' => time() + 86400,
            'iat' => time(),
            'nbf' => time(),
        ];
        $customClaims = JWTFactory::customClaims($data);
        $payload = JWTFactory::make($data);

        $token = JWTAuth::encode($payload);

        PasswordReset::factory()->count(2)->create();

        $resetToken = PasswordReset::factory()
            ->create(
                [
                    'token' => $token->get(),
                ]
            );

        $password = 'Password1234$';
        $passwordConfirmation = 'Password1234$';

        $response = $this->postJson(
            '/api/auth/reset-password/',
            [
                'formData' => [
                    'password' => $password,
                    'password_confirmation' => $passwordConfirmation
                ],
                'formName' => 'resetForm',
                'resetToken' => $resetToken->token,
            ]
        );

        $updatedUser = User::find($user->id);

        $this->assertTrue(Hash::check('Password1234$', $updatedUser->password));
        $this->assertTrue($response->getData()->formSubmitted);
    }
}
