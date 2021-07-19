<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stat;
use App\Models\Profile;


class MessengerControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $currentUser;

    public function setUp(): void
    {
        parent::setUp();
        $this->currentUser = User::factory()
            ->has(Profile::factory())
            ->create(
                [
                    'id' => 1
                ]
            );
    }

    /** @test */
    public function it_gets_contacts_for_the_users_messenger()
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
            ->actingAs($this->currentUser, 'api')
            ->getJson('/api/auth/messenger/' . $this->currentUser->id . '/show', []);

        $response->assertStatus(200);

        $actualContacts = array_map(fn ($contact) => $contact->id, $response->getData()->contacts);
        $expectedContacts = array_keys($currentUserNetwork);

        $this->assertSame($expectedContacts, $actualContacts);
        $this->assertEquals(count($currentUserNetwork), $response->getData()->contacts_count);
    }
}
