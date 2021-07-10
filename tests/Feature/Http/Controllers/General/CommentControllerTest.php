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
use Illuminate\Database\Eloquent\Factories\Sequence;

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


    /** @test */
    public function it_deletes_a_comment_if_user_is_the_author_or_subject()
    {
        $authorCommentUser = User::factory()->create(['id' => 7]);
        $subjectUser = User::factory()->create(['id' => 8]);

        Post::factory()
            ->for($subjectUser)
            ->create(
                [
                    'subject_user_id' => $subjectUser->id,
                    'author_user_id' => User::factory()->create(['id' => 9]),
                ]
            );

        Comment::factory()
            ->count(2)
            ->state(
                new Sequence(
                    [
                        'user_id' => $authorCommentUser->id,
                    ],
                    [
                        'user_id' => User::factory()->create(['id' => 40]),
                    ],
                )
            )
            ->create(['post_id' => $subjectUser->posts[0]]);

        $responses = [];

        for ($i = 0; $i < count($authorized = [$authorCommentUser, $subjectUser]); $i++) {

            $response = $this
                ->actingAs($authorized[$i], 'api')
                ->deleteJson(
                    '/api/auth/comments/' .
                        $subjectUser->posts[0]->comments[$i]->id .
                        '/delete?uid=' .
                        $authorized[$i]->id .
                        '&type=comment',
                    []
                );

            $responses[] = strval($response->getStatusCode()) . ' ' . $response->getData()->msg;
        }

        $subjectUser->refresh();

        $this->assertCount(0, $subjectUser->posts[0]->comments);
        $this->assertSame(['200 successfully deleted', '200 successfully deleted'], $responses);
    }

    /** @test */
    public function it_will_not_delete_comment_if_not_authorized()
    {
        $users = User::factory()
            ->count(3)
            ->create();

        $post = Post::factory()
            ->for($users[2])
            ->create(
                [
                    'author_user_id' => $users[1]->id,
                    'subject_user_id' => $users[2]->id
                ]
            );

        Comment::factory()->for($post)->create(['post_id' => $post->id]);

        $response = $this
            ->actingAs($users[0], 'api')
            ->deleteJson(
                '/api/auth/comments/' .
                    $post->comments[0]->id .
                    '/delete?uid=' .
                    $users[0]->id .
                    '&type=comment',
                []
            );

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'error' => 'Forbidden action: cannot delete comment that is not yours',
            ]
        );
    }

    /** @test */
    public function it_retrieves_comments_for_a_post()
    {

        $users = User::factory()
            ->count(6)
            ->create();

        $post = Post::factory()
            ->create();

        foreach ($users as $key => $user) {

            Profile::factory()
                ->for($user)
                ->create();

            Comment::factory()
                ->for($post)
                ->create(
                    [
                        'user_id' => $user->id,
                        'post_id' => $post->id,
                    ]
                );
        }

        $lastCommentId = $post->comments->count();
        $returnedIds = [];

        for ($i = 0; $i < 2; $i++) {
            if ($i > 0) {
                $lastCommentId = $lastCommentId - 3;
            }
            $response = $this
                ->actingAs($users[0], 'api')
                ->getJson(
                    '/api/auth/posts/' . $post->id . '/comments/show?last=' . $lastCommentId,
                    []
                );

            $response->assertStatus(200);

            array_push($returnedIds, ...array_map(fn ($comment) => $comment->id, $response->getData()->post_comments));
        }

        $this->assertSame([5, 4, 3, 2, 1], $returnedIds);
    }

    /** @test */
    public function it_returns_a_message_if_all_comments_are_loaded()
    {
        $user = User::factory()
            ->create();

        $post = Post::factory()
            ->create();

        Profile::factory()
            ->for($user)
            ->create();

        Comment::factory()
            ->for($post)
            ->create(
                [
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]
            );

        $response = $this
            ->actingAs($user, 'api')
            ->getJson(
                '/api/auth/posts/' .
                    $post->id .
                    '/comments/show?last=' .
                    $post->comments[$post->comments->count() - 1]->id,
                []
            );

        $response->assertStatus(404);
        $response->assertJsonFragment(['error' => 'All comments have been loaded']);
    }
}
