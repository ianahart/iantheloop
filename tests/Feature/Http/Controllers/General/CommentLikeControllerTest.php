<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CommentLikeControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $currentUser;
    protected $commentAuthor;
    protected $comments;

    protected function setUp(): void
    {
        parent::setUp();

        [$this->currentUser, $this->commentAuthor] = User::factory()->count(2)->create();

        Post::factory()
            ->for($this->currentUser)
            ->create(
                [
                    'subject_user_id' => $this->currentUser->id,
                    'author_user_id' => $this->currentUser->id
                ]
            );

        Comment::factory()
            ->for(
                $this->currentUser
                    ->posts[0]
            )
            ->count(2)
            ->state(
                new Sequence(
                    [
                        'id' => 1,
                        'user_id' => $this->commentAuthor->id,
                        'reply_to_comment_id' => null
                    ],
                    [
                        'id' => 2,
                        'user_id' => $this->commentAuthor->id,
                        'reply_to_comment_id' => 1,
                    ]
                )
            )
            ->create();
        $this->comments = [
            [
                'user_id' => $this->currentUser->id,
                'liked_by' => $this->currentUser->full_name,
                'comment_id' => $this->currentUser->posts[0]->comments[0]->id,
                'post_id' => $this->currentUser->posts[0]->id,
                'type' => 'comment',
            ],
            [
                'user_id' => $this->currentUser->id,
                'liked_by' => $this->currentUser->full_name,
                'comment_id' => $this->currentUser->posts[0]->comments[1]->id,
                'post_id' => $this->currentUser->posts[0]->id,
                'parent_id' => $this->currentUser->posts[0]->comments[0]->id,
                'type' => 'reply',
            ],
        ];
    }
    /** @test */
    public function it_stores_a_like_to_a_comment_and_a_reply_comment()
    {
        $commentLikes = [];

        foreach ($this->comments as $index => $comment) {

            $comment['action'] = 'like';

            $response = $this
                ->actingAs($this->currentUser, 'sanctum')
                ->postJson(
                    '/api/auth/comment-likes/store',
                    $comment
                );
            $commentLikes[] = $response->getData()->comment_like;
        }

        $this->currentUser->refresh();

        $response->assertStatus(200);

        foreach ($commentLikes as $index => $commentLike) {
            $this->assertEquals(
                1,
                $this->currentUser->posts[0]->comments[$index]->likes
            );
        }
    }

    /** @test */
    public function it_deletes_a_like_to_a_comment_and_a_reply_comment()
    {

        foreach ($this->currentUser->posts[0]->comments as $comment) {
            CommentLike::factory()->for($comment)->create(['liked_by' => $this->currentUser->full_name, 'user_id' => $this->currentUser->id, 'comment_id' => $comment->id]);
        }

        foreach ($this->currentUser->posts[0]->comments as $cIndex => $comment) {
            $comment['action'] = 'unlike';
            foreach ($comment->commentLikes as $cLIndex => $commentLike) {
                $response = $this
                    ->actingAs($this->currentUser, 'sanctum')
                    ->deleteJson(
                        '/api/auth/comment-likes/' .
                            $commentLike->id .
                            '/delete?commentId=' .
                            $commentLike->comment_id .
                            '&action=' .
                            $comment['action'] .
                            '&userId=' .
                            $this->currentUser->id .
                            '&type=' .
                            $comment['type'],
                        []
                    );
                $response->assertStatus(200);
            }
        }

        $this->currentUser->refresh();

        foreach ($this->currentUser->posts[0]->comments as $index => $comment) {
            $this->assertCount(0, $comment->commentLikes);
        }
    }
}
