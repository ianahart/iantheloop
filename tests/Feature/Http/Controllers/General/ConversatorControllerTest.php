<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use App\Events\MessageSent;
use App\Notifications\UnreadMessage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stat;
use App\Models\Profile;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ConversatorControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $currentUser;
    protected $recipientUser;
    protected $offlineUser;
    protected $newMessage;

    public function setUp(): void
    {
        parent::setUp();

        $this->newMessage = $this->faker()->words(10, true);

        $this->currentUser = User::factory()
            ->has(Profile::factory())
            ->create(
                [
                    'id' => 1,
                    'cur_chat_window_user_id' => 2,
                ]
            );

        [$this->recipientUser, $this->offlineUser] = User::factory()->count(2)
            ->has(Profile::factory()->fullMedia())
            ->state(
                new Sequence(
                    [
                        'id' => 2,
                        'cur_chat_window_user_id' => $this->currentUser->id,
                    ],
                    [
                        'id' => 3,
                        'cur_chat_window_user_Id' => 99,
                    ]
                )
            )->create();
    }

    /** @test */
    public function it_gets_contacts_for_the_users_conversator()
    {

        $otherUsers = User::factory()
            ->count(9)
            ->has(Profile::factory()->fullMedia())
            ->create();

        $otherUsersCollection = $otherUsers->map(function ($user, $key) {
            if ($user->id % 2 === 0) {
                return
                    [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'timestamp' => time(),
                    ];
            }
        });

        $currentUserNetwork = [];
        foreach ($otherUsersCollection as $key => $user) {

            if (!is_null($user)) {
                $currentUserNetwork[$user['id']] = $user;
            }
        }

        foreach ($otherUsers as $otherUser) {

            $statState = $otherUser->id % 2 === 0 ? [
                'following' => [
                    $this->currentUser->id => [
                        'id' => $this->currentUser->id,
                        'name' => $this->currentUser->full_name,
                        'timestamp' => time(),
                    ]
                ],
                'followers' => [
                    $this->currentUser->id => [
                        'id' => $this->currentUser->id,
                        'name' => $this->currentUser->full_name,
                        'timestamp' => time(),
                    ]
                ],
                'following_count' => 1,
                'followers_count' => 1
            ] : [
                'following' => NULL,
                'followers' => NULL,
                'following_count' => 0,
                'followers_count' => 0
            ];

            Stat::factory()
                ->for($otherUser)
                ->create(
                    array_merge(
                        [
                            'user_id' => $otherUser->id,
                            'profile_id' => $otherUser->profile->id
                        ],
                        $statState
                    )
                );
        }

        Stat::factory()
            ->for($this->currentUser)
            ->create(
                [
                    'user_id' => $this->currentUser->id,
                    'following' => $currentUserNetwork,
                    'followers' => $currentUserNetwork,
                    'following_count' => count($currentUserNetwork),
                    'followers_count' => count($currentUserNetwork),
                ]
            );

        $response = $this
            ->actingAs($this->currentUser, 'sanctum')
            ->getJson('/api/auth/conversator/' . $this->currentUser->id . '/show', []);

        $response->assertStatus(200);

        $actualContacts = array_map(fn ($contact) => $contact->id, $response->getData()->contacts);
        $expectedContacts = array_keys($currentUserNetwork);

        $this->assertSame($expectedContacts, $actualContacts);
        $this->assertEquals(count($currentUserNetwork), $response->getData()->contacts_count);
    }

    /** @test */
    public function it_stores_a_users_new_chat_message_from_a_conversation()
    {
        Event::fake();
        Notification::fake();

        $conversation = Conversation::factory()
            ->create(
                ['participants' => $this->currentUser->id . ' ' . $this->recipientUser->id]
            );

        $response = $this->actingAs($this->currentUser, 'sanctum')->postJson(
            '/api/auth/messages',
            [
                'chat_message' => [
                    'recipient' => [
                        'recipient_user_id' => $this->recipientUser->id,
                        'recipient_name' => $this->recipientUser->full_name
                    ],
                    'sender' => [
                        'sender_user_id' => $this->currentUser->id,
                        'sender_name' => $this->currentUser->full_name,
                        'message' => $this->newMessage,
                    ],
                ],
                'conversation_id' => $conversation->participants,
            ]
        );

        $response->assertStatus(200);


        Event::assertDispatched(function (MessageSent $event) {
            return $event->user->id === $this->currentUser->id;
        });

        Notification::assertNotSentTo([$this->recipientUser], UnreadMessage::class);

        $updatedConversation = Conversation::find($response->getData()->conversation_id);
        $latestMessage = $updatedConversation
            ->messages()
            ->latest()
            ->first();

        $this->assertSame($this->newMessage, $latestMessage['message']);
        $this->assertSame($this->recipientUser->id, $latestMessage['recipient_user_id']);
        $this->assertSame($this->currentUser->id, $latestMessage['sender_user_id']);
    }

    /** @test */
    public function it_stores_new_chat_message_from_conversation_and_sends_notification()
    {
        Event::fake();
        Notification::fake();

        $conversation = Conversation::factory()
            ->create(
                ['participants' => $this->currentUser->id . ' ' . $this->offlineUser->id]
            );

        $response = $this->actingAs($this->currentUser, 'sanctum')
            ->postJson(
                '/api/auth/messages',
                [
                    'chat_message' => [
                        'recipient' => [
                            'recipient_user_id' => $this->offlineUser->id,
                            'recipient_name' => $this->offlineUser->full_name
                        ],
                        'sender' => [
                            'sender_user_id' => $this->currentUser->id,
                            'sender_name' => $this->currentUser->full_name,
                            'message' => $this->newMessage,
                        ],
                    ],
                    'conversation_id' => $conversation->participants,
                ]
            );

        $response->assertStatus(200);

        $actualConversation = Conversation::find($response->getData()->conversation_id);
        $this->assertSame($conversation->participants, $actualConversation->participants);

        Notification::assertSentTo(
            $this->offlineUser,
            function (UnreadMessage $notification, $channels) {
                return $notification->message['recipient_user_id'] == $this->offlineUser->id;
            }
        );
        Event::assertDispatched(function (MessageSent $event) {
            return $event->user->id === $this->currentUser->id;
        });
    }

    /** @test */
    public function it_retrieves_all_messages_for_the_current_users_chat_conversation()
    {
        $conversation = Conversation::factory()
            ->create(
                [
                    'id' => 1,
                    'participants' => $this->currentUser->id . ' ' . $this->recipientUser->id
                ]
            );

        for ($i = 0; $i < 21; $i++) {
            Message::factory()
                ->for($conversation)
                ->create([
                    'sender_user_id' =>  $i % 2 === 0 ? $this->currentUser->id : $this->recipientUser->id,
                    'sender_name' => $i % 2 === 0 ? $this->currentUser->full_name : $this->recipientUser->full_name,
                    'recipient_user_id' => $i % 2 === 0 ? $this->recipientUser->id : $this->currentUser->id,
                    'recipient_name' => $i % 2 === 0 ? $this->recipientUser->full_name : $this->currentUser->full_name,
                    'message' => $this->newMessage,
                    'created_at' => now()->addDays($i)->addHours($i),
                ]);
        }

        $pagMessage = $conversation->messages()->orderBy('id', 'DESC')->first();
        $pagMessage->id = $pagMessage->id + 1;

        $messages = [];

        $i = 0;
        while ($i < 2) {

            if ($i > 0) {
                $pagMessage = $messages[count($messages) - 1];
            }

            $response = $this->actingAs($this->currentUser, 'sanctum')
                ->getJson('/api/auth/messages/' .
                    $this->currentUser->cur_chat_window_user_id .
                    '/show?id=' . $pagMessage->id .
                    '&createdAt=' . $pagMessage->created_at);
            $i++;

            $response->assertStatus(200);
            array_push($messages, ...$response->getData()->chat_messages);
        }

        $messages = array_map(fn ($message) => $message->id, $messages);
        $this->assertCount($conversation->messages->count(), $messages);
    }
}
