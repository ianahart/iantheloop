<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Review;
use App\Models\Profile;
use Database\Factories\ReviewFactory;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()
            ->create(['id' => 1]);
        Profile::factory()
            ->fullMedia()
            ->for($this->currentUser)
            ->create(
                [
                    'id' => 1,
                    'user_id' => $this->currentUser->id,
                ]
            );

        Review::factory()
            ->for($this->currentUser)
            ->create(
                [
                    'user_id' => $this->currentUser->id,
                    'rating' => 1,
                    'created_at' => now()->addDays($this->currentUser->id)->addHours($this->currentUser->id),

                ]
            );
    }

    /** @test */
    public function it_stores_a_users_submitted_review()
    {
        $otherUser = User::factory()
            ->has(Review::factory())
            ->create(['id' => 2]);

        $this->currentUser->review->delete();

        $response = $this->actingAs($this->currentUser, 'api')
            ->postJson(
                '/api/auth/reviews/create',
                [

                    'currentUserId' => $this->currentUser->id,
                    'review' => $this->faker()->text(75),
                    'rating' => 4,


                ]
            );

        $response->assertStatus(201);

        $this->currentUser->refresh();
        $this->assertNotNull($this->currentUser->review);
    }

    /** @test */
    public function it_does_not_store_a_duplicate_review()
    {


        $response = $this->actingAs($this->currentUser, 'api')
            ->postJson(
                '/api/auth/reviews/create',
                [
                    'currentUserId' => $this->currentUser->id,
                    'review' => $this->faker()->text(75),
                    'rating' => 4,
                ]
            );

        $response->assertStatus(400);

        $error = $response->getData()->error;
        $this->assertEquals('User has already submitted a review', $error);
    }

    /** @test */
    public function it_retrieves_all_reviews_with_filters_and_paginates()
    {

        $users = User::factory()
            ->count(12)
            ->create();



        $users->prepend($this->currentUser);
        foreach ($users as $idx => $user) {

            if ($user->id > 1) {
                Profile::factory()
                    ->for($user)
                    ->fullMedia()
                    ->create(
                        [
                            'user_id' => $user->id
                        ]
                    );

                Review::factory()
                    ->for($user)
                    ->create(
                        [
                            'user_id' => $user->id,
                            'created_at' => now()->addDays($user->id)->addHours($user->id),
                            'rating' => floor(rand(1, 5)),
                        ]
                    );
            }
        }

        $page = 1;

        $total = count($users);

        $perPage = 5;
        $totalPages = ceil($total / $perPage);

        $ratings = [];
        $actualTotal = 0;

        while ($page <= $totalPages) {

            $response = $this->getJson(
                '/api/auth/reviews/index?page=' . $page . '&filters=highest rated',
                []
            );
            $page = $page + 1;

            $response->assertStatus(200);

            $actualTotal += count($response->getData()->reviews);

            $newRatings = array_map(fn ($review) => $review->rating, $response->getData()->reviews);
            array_push($ratings, ...$newRatings);
        }

        $expectedRatings = [];
        foreach ($users as $user) {
            $expectedRatings[] = $user->review->rating;
        }

        rsort($expectedRatings);

        $this->assertSame($expectedRatings, $ratings);
        $this->assertEquals($total, $actualTotal);
    }

    /** @test */
    public function it_retrieves_the_current_users_review()
    {

        $response = $this->actingAs($this->currentUser, 'api')
            ->getJson(
                '/api/auth/reviews/' . $this->currentUser->id . '/show',
                []
            );

        $response->assertStatus(200);

        $review = $response->getData()->review[0];

        $this->assertEquals($this->currentUser->id, $review->user_id);
        $this->assertEquals($this->currentUser->review->id, $review->id);
    }
    /** @test */
    public function it_updates_a_users_review()
    {
        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->patchJson(
                '/api/auth/reviews/' . $this->currentUser->review->id . '/update',
                [
                    'currentUserId' => $this->currentUser->id,
                    'review' => 'this review has been updated',
                    'rating' => 2,
                ]
            );

        $response->assertStatus(200);

        $this->assertEquals('this review has been updated', $response->getData()->review->text);
        $this->assertEquals(2, $response->getData()->review->rating);
    }

    /** @test */
    public function it_deletes_a_users_review()
    {

        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->deleteJson(
                '/api/auth/reviews/' .
                    $this->currentUser->review->id .
                    '/delete?userId=' .
                    $this->currentUser->id,
                []
            );
        $response->assertStatus(200);

        $this->currentUser->refresh();
        $this->assertNull($this->currentUser->review);
    }

    /** @test */
    public function it_does_not_delete_a_review_if_unauthorized()
    {
        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->deleteJson(
                '/api/auth/reviews/' . $this->currentUser->review->id . '/delete?userId=999',
                []
            );

        $response->assertStatus(500);

        $this->assertEquals('Not authorized to make this transaction', $response->getData()->error);
        $response->assertJsonStructure(['msg', 'error']);
    }
}
