<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_does_not_except_an_invalid_email_address()
    {

        $invalidEmails = ['', 'beth.smith@example..com'];

        $result = [];
        $statusCodes = [];

        foreach ($invalidEmails as $invalidEmail) {
            $response = $this
                ->postJson(
                    '/api/auth/recovery/',
                    [
                        'formData' => [
                            'email' => $invalidEmail,
                            'errors' => [],
                        ],
                        'formName' => 'forgotForm',
                    ]
                );
            array_push($statusCodes, $response->getStatusCode());
            array_push($result, json_decode(json_encode($response->getData()), true));
        }
        $expected = array(
            array(
                'errors' => array(
                    'email' => array(
                        'The email field is required.',
                    ),
                ),
                'formSubmitted' => false,
                'form' => 'forgotForm'
            ),
            array(
                'errors' => array(
                    'email' => array(
                        'The email must be a valid email address.',
                    )
                ),
                'formSubmitted' => false,
                'form' => 'forgotForm'
            )
        );

        $this->assertSame([422, 422], $statusCodes);
        $this->assertSame($expected, $result);
    }

    /** @test */
    public function it_does_not_send_email_to_non_existent_email_address()
    {
        $emails = ['delaroose@aol.com', 'beth.rippey@gmail.com'];
        $users = [];

        foreach ($emails as $email) {
            array_push(
                $users,
                User::factory()
                    ->create(
                        ['email' => $email]
                    )
            );
        }

        $response = $this
            ->postJson(
                '/api/auth/recovery/',
                [
                    'formData' => [
                        'email' => 'fakeemail@example.com',
                        'errors' => [],
                    ],
                    'formName' => 'forgotForm',
                ]
            );

        $this->assertEquals('Email does not exist', $response->getData()->errors->email[0]);
        $response->assertStatus(404);
    }

    /** @test */

    public function it_sends_a_reset_email_to_valid_email_address()
    {
        $user = User::factory()->create(
            [
                'email' => 'beth.rippey@gmail.com',
            ]
        );

        $response = $this->postJson(
            '/api/auth/recovery',
            [
                'formData' => [
                    'email' => 'beth.rippey@gmail.com',
                    'errors' => [],
                ],
                'formName' => 'forgotForm',
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                'status' => 'Email sent',
                'formSubmitted' => true,
            ]
        );
    }
}
