<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Stat;

class NetworkControllerTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    protected $subjectUser;
    protected $currentUser;
    protected $otherUsers;

    public function setUp(): void
    {
        parent::setUp();

        [$this->subjectUser, $this->currentUser] = User::factory()
            ->count(2)
            ->has(Profile::factory()->fullMedia())
            ->create();

        $this->otherUsers =
            User::factory()
            ->count(9)
            ->has(Profile::factory()->fullMedia())
            ->create();

        $this->otherUsers->prepend($this->currentUser);

        foreach ($this->otherUsers as $key => $otherUser) {

            $subjectFollowingOtherUser = $otherUser->id % 2 === 0 ? [$this->subjectUser->id => [
                'id' => $this->subjectUser->id,
                'name' => $this->subjectUser->full_name,
                'timestamp' => time(),
            ]] : null;

            Stat::factory()
                ->for($otherUser)
                ->create(
                    [
                        'profile_id' => $otherUser->profile->id,
                        'user_id' => $otherUser->id,
                        'following_count' => 1,
                        'following' => [
                            $this->subjectUser->id => [
                                'id' => $this->subjectUser->id,
                                'name' => $this->subjectUser->full_name,
                                'timestamp' => time(),
                            ]
                        ],
                        'followers_count' => $otherUser->id % 2 === 0 ? 1 : 0,
                        'followers' => $subjectFollowingOtherUser,
                    ]
                );

            $subjectFollowers[$otherUser->id] = [
                'id' => $otherUser->id,
                'name' => $otherUser->full_name,
                'timestamp' => time(),
            ];

            if ($otherUser->id % 2 === 0) {
                $subjectFollowing[$otherUser->id] = [
                    'id' => $otherUser->id,
                    'name' => $otherUser->full_name,
                    'timestamp' => time(),
                ];
            }
        }

        $this->currentUser = $this->otherUsers[0];

        Stat::factory()
            ->for($this->subjectUser)
            ->create(
                [
                    'profile_id' => $this->subjectUser->profile->id,
                    'user_id' => $this->subjectUser->id,
                    'followers' => $subjectFollowers,
                    'followers_count' => count($subjectFollowers),
                    'following' => $subjectFollowing,
                    'following_count' => count($subjectFollowing),
                ]
            );
    }

    /** @test */
    public function it_returns_the_user_being_viewed_on_page_followers_list()
    {

        $lastFollower = $this->subjectUser->stat->followers[count($this->subjectUser->stat->followers) + 1];
        $page = 1;
        $index = 0;
        $responses = [];

        while ($index < $lastFollower['id']) {
            $response = $this
                ->actingAs($this->currentUser, 'api')
                ->getJson(
                    '/api/auth/network/followers/show/' .
                        $this->subjectUser->id .
                        '?page=' . $page . '&index=' .  $index,
                    []
                );

            $index = $response->getData()->profiles[count($response->getData()->profiles) - 1];
            $index = $index->user_id;
            $page++;

            $response->assertStatus(200);
            $responses[] = $response->getData()->profiles;
        }

        $this->subjectUser->refresh();
        $profiles = array_merge([], ...$responses);
        $this->assertCount(10, $profiles);

        $actualFollowers = array_map(fn ($follower) => $follower->user_id, $profiles);

        $expectedFollowers = array_combine(
            range(0, count($this->subjectUser->stat->followers) - 1),
            array_map(fn ($follower) => $follower['id'], $this->subjectUser->stat->followers)
        );

        $this->assertSame($expectedFollowers, $actualFollowers);
    }

    /** @test */
    public function it_returns_the_user_being_viewed_on_page_following_list()
    {
        $lastFollowing = array_key_last($this->subjectUser->stat->following);
        $lastFollowing = $this->subjectUser->stat->following[$lastFollowing];

        $page = 1;
        $index = 0;
        $responses = [];

        while ($index < $lastFollowing['id']) {
            $response = $this
                ->actingAs($this->currentUser, 'api')
                ->getJson(
                    '/api/auth/network/following/show/' .
                        $this->subjectUser->id .
                        '?page=' . $page . '&index=' .  $index,
                    []
                );

            $index = $response->getData()->profiles[count($response->getData()->profiles) - 1];
            $index = $index->user_id;
            $page++;

            $response->assertStatus(200);
            $responses[] = $response->getData()->profiles;
        }

        $actualFollowing = array_map(fn ($user) => $user->user_id,  array_merge([], ...$responses));
        $this->assertCount(5, $actualFollowing);

        $expectedFollowing = array_values(array_map(fn ($user) => $user['id'], $this->subjectUser->stat->following));

        $this->assertSame($expectedFollowing, $actualFollowing);
    }
}
