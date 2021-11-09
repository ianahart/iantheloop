<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\FollowRequest;
use App\Models\Stat;

class StatsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $currentUser;
    protected $viewingUser;
    protected $userToBeUnfollowed;

    public function setUp(): void
    {

        parent::setUp();

        [$this->viewingUser, $this->currentUser, $this->userToBeUnfollowed] = User::factory()
            ->count(3)
            ->has(Profile::factory()->fullMedia())
            ->create();

        foreach ([$this->viewingUser, $this->currentUser] as $index => $user) {

            Stat::factory()
                ->for($user)
                ->create(
                    $index === 0 ? [
                        'profile_id' => $user->profile->id,
                        'user_id' => $user->id,
                        'name' => $user->full_name,
                    ] : [
                        'profile_id' => $user->profile->id,
                        'user_id' => $user->id,
                        'following_count' => 1,
                        'name' => $user->full_name,
                        'following' => [
                            $this->userToBeUnfollowed->id => [
                                'id' => $this->userToBeUnfollowed->id,
                                'name' => $this->userToBeUnfollowed->full_name,
                                'timestamp' => time(),
                            ]
                        ],
                    ]
                );
        }

        Stat::factory()
            ->for($this->userToBeUnfollowed)
            ->create(
                [
                    'profile_id' => $this->userToBeUnfollowed->profile->id,
                    'following_count' => 0,
                    'followers_count' => 1,
                    'user_id' => $this->userToBeUnfollowed->id,
                    'notifications' => [],
                    'followers' =>  [
                        $this->currentUser->id => [
                            'id' => $this->currentUser->id,
                            'name' => $this->currentUser->full_name,
                            'timestamp' => time()
                        ],
                    ],
                    'following' => null
                ]
            );

        FollowRequest::factory()
            ->for($this->viewingUser)
            ->create(
                [
                    'requester_user_id' => $this->currentUser->id,
                    'receiver_user_id' => $this->viewingUser->id,
                ]
            );
    }

    /** @test */
    public function it_updates_the_follow_stats_of_user_requested_and_user_received()
    {

        $response = $this
            ->actingAs($this->viewingUser, 'sanctum')
            ->patchJson(
                '/api/auth/stats/follow/' . $this->currentUser->id .  '/update',

                [
                    'viewingUserId' => $this->viewingUser->followReceives[0]->receiver_user_id,
                    'currentUserId' => $this->viewingUser->followReceives[0]->requester_user_id,
                    'requestId' => $this->viewingUser->followReceives[0]->id,
                ]
            );

        $response->assertStatus(200);

        $this->viewingUser->refresh();

        $viewingUserFollowers = json_decode(
            json_encode(
                $response->getData()->stats->followers
            ),
            true
        );

        $this->assertEquals($this->currentUser->id, array_shift($viewingUserFollowers)['id']);

        $this->assertTrue(in_array($this->viewingUser->id, array_merge([], ...$this->currentUser->stat->following)));
    }

    /** @test */
    public function it_updates_the_stats_of_the_two_users_when_one_user_is_unfollowed()
    {

        $response = $this
            ->actingAs($this->currentUser, 'sanctum')
            ->patchJson(
                '/api/auth/stats/unfollow/' . $this->currentUser->id . '/update',
                [
                    'viewingUserId' => $this->userToBeUnfollowed->id,
                ]
            );

        $response->assertStatus(200);

        $this->currentUser->refresh();
        $this->userToBeUnfollowed->refresh();

        $this->assertCount(0, $this->userToBeUnfollowed->stat->followers);
        $this->assertCount(0, $this->currentUser->stat->following);

        $actualNotification = json_decode(
            json_encode($response->getData()->stats->notifications),
            true
        )[$this->currentUser->full_name]['notification'];

        $this->assertEquals($this->currentUser->full_name . ' has unfollowed you.', $actualNotification);
    }
}
