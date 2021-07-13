<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\FollowRequest;

class FollowRequestControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $currentUser;
    protected $otherUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()
            ->has(Profile::factory()->fullMedia())
            ->create();

        $this->otherUsers = User::factory()
            ->count(2)
            ->has(Profile::factory()->fullMedia())
            ->create();

        foreach ($this->otherUsers as $key => $otherUser) {
            FollowRequest::factory()
                ->for($otherUser)
                ->create(
                    [
                        'receiver_user_id' => $this->currentUser->id,
                        'requester_user_id' => $otherUser->id
                    ]
                );
        }
    }

    /** @test */
    public function it_retrieves_all_follow_requests_for_the_current_user()
    {
        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->getJson('/api/auth/follow-requests/index?userId=' . $this->currentUser->id, []);

        $response->assertStatus(200);
        $this->assertSame(
            [2, 3],
            array_map(fn ($request) => intval($request->requester_user_id), $response->getData()->follow_requests)
        );
    }
    /** @test */
    public function it_stores_a_users_follow_request()
    {

        $response = $this->actingAs($this->currentUser, 'api')->postJson(
            '/api/auth/follow-requests/store',
            [
                'requester_user_id' => $this->currentUser->id,
                'receiver_user_id' => $this->otherUsers[0]->id,
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonFragment(['follow_status' => 'Requested']);

        $this->otherUsers[0]->refresh();
        $this->assertEquals(2, $this->currentUser->followRequests[0]->receiver_user_id);
    }

    /** @test */
    public function it_deletes_a_users_follow_request()
    {

        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->deleteJson(
                '/api/auth/follow-requests/' .
                    $this->currentUser->followReceives[0]->id .
                    '/delete?userId=' .
                    $this->currentUser->followReceives[0]->receiver_user_id,
                []
            );

        $response->assertStatus(200);
        $this->currentUser->refresh();

        $this->assertEquals(1, $this->currentUser->followReceives->count());
    }
}
