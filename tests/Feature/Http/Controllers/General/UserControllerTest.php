<?php

namespace Tests\Feature\Http\Controllers\General;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use App\Models\Review;
use App\Events\UserStatusChanged;
use Database\Factories\ReviewFactory;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public $token;
    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->create(
                ['status' => 'offline', 'id' => 40]
            );
        $this->token = JWTAuth::fromUser($this->user);
    }



    /** @test */
    public function it_updates_the_users_status()
    {
        Event::fake();

        $response = $this
            ->withHeaders(
                [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token
                ]
            )
            ->patchJson(
                '/api/auth/user/status/' . $this->user->id . '/update',
                ['status' => 'online']
            );

        $response->assertStatus(200);
        Event::assertDispatched(UserStatusChanged::class);
        $response->assertJsonFragment(
            [
                'new_user_status' => 'online',
                'status_updated' => true
            ]
        );
        $this->user->refresh();
        $this->assertEquals($this->user->status, $response->getData()->new_user_status);
    }

    /** @test */
    public function it_only_allows_current_user_to_update_status()
    {

        $response = $this
            ->withHeaders(
                [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token
                ]
            )
            ->patchJson(
                '/api/auth/user/status/39/update',
                ['status' => 'online']
            );

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'error' => 'User is forbidden from making this request',
                'status_updated' => false,
            ]
        );
    }

    /** @test */
    public function it_counts_total_users_and_total_reviews()
    {

        $users = User::factory()
            ->count(4)
            ->create();

        $ratings = [];
        foreach ($users as $idx => $user) {
            $rating = $idx % 2 === 0 ? 3 : 4;
            $ratings[] = $rating;

            Review::factory()
                ->for($user)
                ->count(2)
                ->create(
                    [
                        'user_id' => $user->id,
                        'rating' => $rating,
                    ]
                );
        }

        $avgRating = round(array_sum($ratings) / count($ratings), 1);

        $response = $this->getJson('/api/auth/users/count', []);

        $data = $response->getData();

        $this->assertEquals($users->count() + 1, $data->count);
        $this->assertEquals(8, $data->review_count);

        $this->assertEquals($avgRating, $data->avg_review_rating);
    }
}
