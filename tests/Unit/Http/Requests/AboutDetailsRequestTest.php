<?php

namespace Tests\Unit\Http\Requests;

use PHPUnit\Framework\TestCase;
use App\Http\Requests\AboutDetailsRequest;

class AboutDetailsRequestTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new AboutDetailsRequest();
    }

    public function testRules()
    {
        $this->assertEquals(
            [
                'aboutDetails.bio' => ['nullable', 'max:500', 'regex:/^[\.a-zA-Z0-9,!?\' ]*$/'],
                'aboutDetails.interests.*.name' => ['nullable', 'max:75', 'min:2', 'regex:/(^[A-Za-z0-9 ]+$)+/'],
            ],
            $this->subject->rules()
        );
    }

    public function testMessages()
    {
        $this->assertEquals(
            [
                'aboutDetails.bio.max' => 'The maximum number of characters for the bio is (500)',
                'aboutDetails.bio.regex' => 'Please use only letters, numbers, spaces, apostrophes, and punctuation',
                'aboutDetails.interests.*.name.min' => 'The minimum number of characters for an interest is (2)',
                'aboutDetails.interests.*.name.max' => 'The maximum number of characters for an interest is (75)',
                'aboutDetails.interests.*.name.regex' => 'Please use only letters, numbers, and spaces',
            ],
            $this->subject->messages()
        );
    }

    public function testAuthorize()
    {
        $this->assertTrue($this->subject->authorize());
    }
}
// Tests: 24, Assertions: 71, Warnings: 1.