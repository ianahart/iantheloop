<?php

namespace Tests\Unit\Http\Requests;

use PHPUnit\Framework\TestCase;
use App\Http\Requests\RegisterRequest;

class RegisterRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new RegisterRequest();
    }
    public function testRules()
    {
        $this->assertEquals(
            [
                'formData.firstName' => ['required', 'min:2', 'max:40', 'regex:/^[a-zA-Z .]+$/'],
                'formData.lastName' => ['required', 'min:2', 'max:80', 'regex:/^[a-zA-Z .]+$/'],
                'formData.email' => 'required|email',
                'formData.password' => ['required', 'confirmed', 'min:6', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/'],
                'formData.password_confirmation' => 'required|min:6',
            ],

            $this->subject->rules()
        );
    }

    public function testAuthorize()
    {
        $this->assertTrue($this->subject->authorize());
    }
}
