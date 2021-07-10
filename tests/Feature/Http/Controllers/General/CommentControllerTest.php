<?php

namespace Tests\Feature\Http\Controllers\General;

use App\Http\Controllers\General\CommentController;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use JMac\Testing\Traits\AdditionalAssertions;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;


class CommentControllerTest extends TestCase
{

    use RefreshDatabase;
    use AdditionalAssertions;
    use WithFaker;

    /** @test */
    public function it_stores_a_comment_on_a_post()
    {
        $this->assertActionUsesFormRequest(CommentController::class, 'store', StoreCommentRequest::class);

        $user = User::factory()->create();
        Profile::factory()->fullMedia()
            ->create(
                [
                    'user_id' => $user->id
                ]
            );
        $post = Post::factory()->create();

        $response = $this
            ->actingAs($user, 'api')
            ->postJson(
                '/api/auth/comments/store',
                [
                    'input' => $this->faker->text(30),
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]
            );

        $response->assertStatus(201);
        $response->assertJsonStructure(
            [
                'latest_comment' => [
                    'id',
                    'user_id',
                    'post_id',
                    'comment_text',
                    'likes',
                    'reply_to_comment_id',
                    'profile_picture',
                    'full_name',
                    'reply_comments',
                    'reply_comments_count'
                ]
            ]
        );

        $response->assertJson(
            [
                'latest_comment' => [
                    'id' => 1,
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]
            ]
        );
    }

    /** @test */
    public function it_only_allows_five_comments_on_same_post_per_five_minutes_by_same_user()
    {

        $user = User::factory()
            ->create(
                [
                    'id' => 2
                ]
            );

        Profile::factory()
            ->for($user)
            ->create();

        $post = Post::factory()->create();

        Comment::factory()
            ->for($post)
            ->count(5)
            ->create(
                [
                    'user_id' => $user->id
                ]
            );

        $response = $this->actingAs($user, 'api')->postJson(
            '/api/auth/comments/store',
            [
                'input' => $this->faker->text(30),
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]
        );
        $response->assertStatus(400);

        $this->assertEquals('Failed to add comment', $response->getData()->msg);
        $this->assertEquals('Maximum of 5 comments on the same post per 5 minutes.', $response->getData()->error[0]);
    }
}
