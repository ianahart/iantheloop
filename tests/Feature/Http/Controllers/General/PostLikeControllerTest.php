<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\PostLike;


class PostLikeControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $subjectUser;
    protected $currentUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()
            ->create(
                [
                    'id' => 2
                ]
            );

        $this->subjectUser = User::factory()
            ->has(Post::factory()
                ->count(2)
                ->uploadedPhoto()
                ->state(
                    [
                        'author_user_id' => $this->currentUser->id,
                        'subject_user_id' => 1,

                    ]
                ))
            ->create(
                [
                    'id' => 1
                ]
            );
    }

    /** @test */
    public function it_likes_a_post_on_a_users_profile()
    {
        $response = $this->actingAs($this->currentUser, 'api')
            ->postJson(
                '/api/auth/post-likes/store',
                [
                    'current_user_id' => $this->currentUser->id,
                    'post_id' => $this->subjectUser->posts[0]->id,
                ]
            );

        $response->assertStatus(201);

        $this->assertEquals(1, $response->getData()->new_like->post->likes);
        $this->assertEquals($this->currentUser->id, $response->getData()->new_like->user_id);
    }

    /** @test */
    public function it_unlikes_a_post_on_a_users_profile()
    {
        PostLike::factory()
            ->for($this->subjectUser->posts[0])->create(
                [
                    'user_id' => $this->currentUser->id,
                    'liker_name' => $this->currentUser->full_name
                ]
            );

        $response = $this->actingAs($this->currentUser, 'api')
            ->deleteJson(
                '/api/auth/post-likes/' .
                    $this->subjectUser->posts[0]->postLikes[0]->id .
                    '/delete?userId=' .
                    $this->currentUser->id,
                []
            );

        $response->assertStatus(204);

        $this->assertEquals('like removed from post', $response->getData()->msg);
    }
}
