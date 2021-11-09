<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use App\Notifications\Interaction;
use App\Notifications\UnreadMessage;
use Database\Factories\NotificationFactory;
use Illuminate\Notifications\DatabaseNotification;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public $currentUser;
    public $postAuthor;

    public function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()->create(['id' => 1]);
        $this->postAuthor = User::factory()->create(['id' => 2]);

        Profile::factory()
            ->for($this->currentUser)
            ->create(
                [
                    'id' => 1,
                    'user_id' => $this->currentUser->id
                ]
            );

        Profile::factory()->for($this->postAuthor)
            ->fullMedia()
            ->create(
                [
                    'id' => 2,
                    'user_id' => $this->postAuthor->id
                ]
            );

        Post::factory()->for($this->currentUser)
            ->uploadedPhoto()
            ->create(
                [

                    'subject_user_id' => $this->currentUser->id,
                    'author_user_id' => $this->postAuthor->id,
                    'post_text' => $this->faker()->text(40),
                    'likes' => 2,
                    'comments_count' => 2,
                ]
            );
    }

    /** @test */
    public function it_retrieves_interaction_notifications_for_the_current_user()
    {

        $usersSending = User::factory()
            ->count(5)
            ->create();

        foreach ($usersSending as $userSending) {

            Profile::factory()->for($userSending)
                ->fullMedia()
                ->create(
                    ['user_id' => $userSending->id]
                );

            NotificationFactory::new()->create(
                [
                    'id' => Str::uuid(),
                    'notifiable_id' => $this->currentUser->id,
                    'type' => 'App\Notifications\Interaction',
                    'created_at' => now()->addDays(-$userSending->id + 1)->addHours(-$userSending->id),
                    'read_at' => NULL,
                    'data' => [
                        'sender_name' => $userSending->full_name,
                        'recipient_name' => $this->currentUser->full_name,
                        'sender_user_id' => $userSending->id,
                        'recipient_user_id' => $this->currentUser->id,
                        'post_id' => $this->currentUser->posts[0]->id,
                        'photo_link' => $this->currentUser->posts[0]->photo_link,
                        'text' => $userSending->full_name . ' commented on a post on your wall.',
                    ],
                ]
            );
        }

        $incrementer = 1;
        $pages = ceil($this->currentUser->notifications->count() / 3);

        $cursor = NULL;
        $initialEndpoint = '/api/auth/user/notifications/interactions/' .
            $this->currentUser->id .
            '/show?cursor=&type=App/Notifications/Interaction';

        $actualNotifications = collect([]);

        while ($incrementer <= $pages) :
            $response = $this->actingAs($this->currentUser, 'sanctum')
                ->getJson(
                    is_null($cursor) ? $initialEndpoint : $cursor . '&type=App/Notifications/Interaction',
                    []
                );

            $cursor = $response->getData()->next_page_url;

            $actualNotifications->push(...array_map(function ($notification) {

                return json_decode(json_encode($notification->data), true);
            }, $response->getData()->notifications));

            $response->assertStatus(200);
            $response->assertJsonStructure(['msg', 'notifications', 'next_page_url']);

            $incrementer++;
        endwhile;


        $expectedNotifications = $this->currentUser->notifications->map(function ($item) {

            return $item->data;
        });

        $containsReadableDates = collect([]);

        foreach ($actualNotifications as $notification) {
            $containsReadableDates->push(str_contains($notification['readable_date'], 'ago'));
        }

        $this->assertSame([true, true, true, true, true], $containsReadableDates->toArray());

        $actualNotifications = $actualNotifications->map(function ($item) {

            unset($item['readable_date']);
            return $item;
        });

        $this->assertEquals($expectedNotifications, $actualNotifications);
    }

    /** @test */
    public function it_retrieves_unreadmessage_notifications_for_the_current_user()
    {
        $sendingUsers = User::factory()->count(7)->create();

        foreach ($sendingUsers as $sendingUser) {

            Profile::factory()
                ->for($sendingUser)
                ->fullMedia()
                ->create(['user_id' => $sendingUser->id]);

            $count = $sendingUser->id === 3 ? 2 : 1;

            NotificationFactory::new()->count($count)->create([
                'notifiable_id' => $this->currentUser->id,
                'type' => 'App\Notifications\UnreadMessage',
                'created_at' => now()->addDays(-$sendingUser->id + 1)->addHours(-$sendingUser->id),
                'read_at' => $sendingUser->id === 3 ? now()->addDays(-$sendingUser->id + 1)->addHours(-$sendingUser->id + 1) : NULL,
                'data' => [
                    'sender_name' => $sendingUser->full_name,
                    'recipient_name' => $this->currentUser->full_name,
                    'sender_user_id' => $sendingUser->id,
                    'recipient_user_id' => $this->currentUser->id,
                    'status' => 'offline',
                    'profile_picture' => $sendingUser->profile->profile_picture,
                ]
            ]);
        }

        $lastPage = ceil(($this->currentUser->notifications->count() - 1) / 3);

        $currentPage = 1;

        $actualNotifications = collect([]);

        while ($currentPage <= $lastPage) :

            $response = $this->actingAs($this->currentUser, 'sanctum')
                ->getJson(
                    '/api/auth/user/notifications/messages/' .
                        $this->currentUser->id .
                        '/show?page=' . $currentPage .
                        '&type=App/Notifications/UnreadMessage',
                    []
                );

            $response->assertStatus(200);
            $actualNotifications->push(...array_map(
                fn ($notification) => json_decode(json_encode($notification), true),
                $response->getData()->notifications
            ));

            $currentPage++;
        endwhile;

        $correctRecipient = $actualNotifications->map(function ($item) {
            return intval($item['recipient_user_id']) === intval($this->currentUser->id);
        });

        $actualReadAtColumn = NULL;
        foreach ($actualNotifications as $actualNotification) {
            if (!is_null($actualNotification['latest_read_at'])) {
                $actualReadAtColumn = $actualNotification;
                break;
            }
        }

        $this->assertStringContainsString('ago', $actualReadAtColumn['latest_read_at']);

        $this->assertCount($this->currentUser->notifications->count() - 1, $correctRecipient);
        $this->assertCount($this->currentUser->notifications->count() - 1, $actualNotifications);
    }

    /** @test */
    public function it_updates_a_message_notification_to_read()
    {

        NotificationFactory::new()->create([
            'notifiable_id' => $this->currentUser->id,
            'type' => 'App\Notifications\UnreadMessage',
            'created_at' => now()->addDays(-$this->currentUser->id + 1)->addHours(-$this->currentUser->id),
            'data' => [
                'recipient_user_id' => $this->currentUser->id,
                'recipient_name' => $this->currentUser->full_name,
                'status' => 'offline',

                'sender_name' => $this->postAuthor->full_name,
                'sender_user_id' => $this->postAuthor->id,
            ],
        ]);

        $response = $this->actingAs($this->currentUser, 'sanctum')
            ->patchJson(
                '/api/auth/user/notifications/messages/' . $this->currentUser->id . '/update',
                [
                    'sender' => $this->postAuthor->id,
                    'type' => 'App/Notifications/UnreadMessage',
                ]
            );

        $response->assertStatus(204);
        $this->assertNotNull($this->currentUser->notifications[0]->read_at);
    }

    /** @test */
    public function it_removes_a_current_users_message_notification()
    {
        NotificationFactory::new()->create([
            'notifiable_id' => $this->currentUser->id,
            'type' => 'App\Notifications\UnreadMessage',
            'created_at' => now()->addDays(-$this->currentUser->id + 1)->addHours(-$this->currentUser->id),
            'data' => [
                'recipient_user_id' => $this->currentUser->id,
                'recipient_name' => $this->currentUser->full_name,
                'status' => 'offline',

                'sender_name' => $this->postAuthor->full_name,
                'sender_user_id' => $this->postAuthor->id,
            ],
        ]);

        $response = $this->actingAs($this->currentUser, 'sanctum')
            ->deleteJson(
                '/api/auth/user/notifications/messages/' .
                    $this->currentUser->id .
                    '/delete?sender=' .
                    $this->postAuthor->id .
                    '&type=App/Notifications/UnreadMessage',
                []
            );

        $response->assertStatus(200);

        $this->currentUser->refresh();
        $this->assertCount(0, $this->currentUser->notifications);
    }

    /** @test */
    public function it_deletes_a_current_users_interaction_notification()
    {

        NotificationFactory::new()->count(2)->create([
            'notifiable_id' => $this->currentUser->id,
            'type' => 'App\Notifications\Interaction',
            'created_at' => now()->addDays(-$this->currentUser->id + 1)->addHours(-$this->currentUser->id),
            'read_at' => NULL,
            'data' => [
                'sender_name' => $this->postAuthor->full_name,
                'recipient_name' => $this->currentUser->full_name,
                'sender_user_id' => $this->postAuthor->id,
                'recipient_user_id' => $this->currentUser->id,
                'post_id' => $this->currentUser->posts[0]->id,
                'photo_link' => $this->currentUser->posts[0]->photo_link,
                'text' => $this->postAuthor->full_name . ' commented on a post on your wall.',
            ],
        ]);

        $response = $this->actingAs($this->currentUser, 'sanctum')
            ->deleteJson(
                '/api/auth/user/notifications/interactions/' .
                    $this->currentUser->notifications[0]->id .
                    '/delete?userId=' .
                    $this->currentUser->id,
                []
            );

        $response->assertStatus(200);

        $this->currentUser->refresh();
        $this->assertCount(1, $this->currentUser->notifications);
    }

    /** @test */
    public function it_retrieves_notification_alerts_count()
    {

        for ($i = 0; $i < 5; $i++) {
            NotificationFactory::new()->create([
                'notifiable_id' => $this->currentUser->id,
                'type' => $i % 2 === 0 ? 'App\Notifications\UnreadMessage' : 'App\Notifications\Interaction',
                'created_at' => now()->addDays(-$this->currentUser->id + 1)->addHours(-$this->currentUser->id),
                'data' => [
                    'recipient_user_id' => $this->currentUser->id,
                    'recipient_name' => $this->currentUser->full_name,
                    'status' => 'offline',

                    'sender_name' => $this->postAuthor->full_name,
                    'sender_user_id' => $this->postAuthor->id,
                ],
            ]);
        }
        $type = base64_encode(
            json_encode(
                [
                    'type' => ['App/Notifications/UnreadMessage', 'App/Notifications/Interaction'],
                ]
            )
        );

        $response = $this->actingAs($this->currentUser, 'sanctum')
            ->getJson('/api/auth/user/notifications/alerts/' .
                $this->currentUser->id .
                '/show?type=' .
                $type, []);

        $response->assertStatus(200);
        $response->assertJsonStructure(['msg', 'nav_interaction_alerts', 'nav_message_alerts']);

        $this->assertEquals(
            $this->currentUser->notifications
                ->where('type', '=', "App\Notifications\Interaction")->count(),
            $response->getData()->nav_interaction_alerts
        );

        $this->assertTrue($response->getData()->nav_message_alerts);
    }
}
