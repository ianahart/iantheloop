<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use App\Helpers\CustomValidator;

class CustomValidatorTest extends TestCase
{
    protected $expectedRules;
    protected $input;
    protected $customValidator;
    protected $expectedMessages;

    public function setUp(): void
    {
        parent::setUp();

        $this->expectedRules = [
            'nullable',
            'regex:/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/',
            'max:70'
        ];
        $this->expectedMessages = [
            'Maximum allowed characters is 50',
            'The URL format is invalid',
        ];

        $this->input
            = [
                'town' => 'gilford',
                'state' => 'california',
                'display_name' => 'displayname12',
                'url-1' => 'www.google.com',
                'url-2' => 'www.apple.com',
                'url-3' => 'www.example.com',
            ];

        $this->customValidator = new CustomValidator($this->input);
    }

    /** @test */
    public function it_returns_the_correct_link_rules_for_create_profile()
    {

        $this->customValidator->setFormName('generalDetails');
        $this->customValidator->generateLinkRules();

        $rules = $this->customValidator->getRules();

        $this->assertIsArray($rules);

        $this->assertSame(
            [
                'generalDetails.url-1' => $this->expectedRules,
                'generalDetails.url-2' => $this->expectedRules,
                'generalDetails.url-3' => $this->expectedRules,
            ],
            $rules
        );
    }

    /** @test */
    public function it_returns_the_correct_link_rules_for_edit_profile()
    {
        $this->customValidator->setFormName('');
        $this->customValidator->generateLinkRules();

        $rules = $this->customValidator->getRules();

        $this->assertIsArray($rules);

        $this->assertSame(
            [
                'url-1' => $this->expectedRules,
                'url-2' => $this->expectedRules,
                'url-3' => $this->expectedRules,
            ],
            $rules
        );
    }

    /** @test */
    public function it_returns_the_correct_link_messages_for_create_profile()
    {
        $this->customValidator->setFormName('generalDetails');
        $this->customValidator->generateLinkMessages();

        $messages = $this->customValidator->getMessages();

        $this->assertIsArray($messages);
        $this->assertSame(
            [
                'generalDetails.url-1.max' => $this->expectedMessages[0],
                'generalDetails.url-1.regex' => $this->expectedMessages[1],
                'generalDetails.url-2.max' => $this->expectedMessages[0],
                'generalDetails.url-2.regex' => $this->expectedMessages[1],
                'generalDetails.url-3.max' => $this->expectedMessages[0],
                'generalDetails.url-3.regex' => $this->expectedMessages[1],
            ],
            $messages
        );
    }

    /** @test */
    public function it_returns_the_correct_link_messages_for_edit_profile()
    {
        $this->customValidator->setFormName('');
        $this->customValidator->generateLinkMessages();

        $messages = $this->customValidator->getMessages();

        $this->assertIsArray($messages);
        $this->assertSame(
            [
                'url-1.max' => $this->expectedMessages[0],
                'url-1.regex' => $this->expectedMessages[1],
                'url-2.max' => $this->expectedMessages[0],
                'url-2.regex' => $this->expectedMessages[1],
                'url-3.max' => $this->expectedMessages[0],
                'url-3.regex' => $this->expectedMessages[1],
            ],
            $messages
        );
    }
}
