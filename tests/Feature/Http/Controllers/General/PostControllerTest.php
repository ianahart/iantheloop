<?php

namespace Tests\Feature\Http\Controllers\General;

use App\Http\Controllers\General\PostController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePostRequest;
use App\Jobs\ProcessInteraction;
use App\Models\User;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Sequence;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;



class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    use AdditionalAssertions;
    use WithFaker;

    protected $videoFile;

    public function setUp(): void
    {
        parent::setUp();
        $this->videoFile = new UploadedFile(resource_path('assets/test_vid.mp4'), 'test_vid.mp4', 'video/mp4', null, true);
    }

    /** @test */
    public function it_stores_a_users_new_post()
    {
        $this->assertActionUsesFormRequest(PostController::class, 'store', StorePostRequest::class);

        Bus::fake();

        $newPost = [
            'subject_user_id' => 20,
            'author_user_id' => 7,
            'post_text' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.',

        ];

        $authorUser = User::factory()->has(Profile::factory())->create(['id' => 7]);
        $subjectUser = User::factory()->has(Profile::factory())->create(['id' => 20]);

        Storage::fake('s3');

        /**@var mixed $authorUser */
        $response = $this
            ->actingAs($authorUser, 'api')
            ->postJson(
                '/api/auth/posts/store',
                [
                    'data' => json_encode($newPost),
                    'videofile' => $this->videoFile,
                    'photofile' => null,

                ]
            );

        $response->assertStatus(201);
        Bus::assertDispatched(ProcessInteraction::class);
        $response->assertJsonFragment(
            [
                'subject_user_id' => $newPost['subject_user_id'],
                'author_user_id' => $newPost['author_user_id'],
                'post_text' => $newPost['post_text'],
            ]
        );

        $this->assertEquals(1, preg_match('/.mp4/', $response->getData()->new_post->video_link));

        $subjectUser->refresh();
        $this->assertEquals(1, $subjectUser->posts->count());
    }

    /** @test */
    public function it_deletes_a_post_if_user_is_subject_or_author()
    {


        $subjectUser = User::factory()->create(['id' => 7]);
        $authorUser = User::factory()->create(['id' => 8]);

        Storage::fake('s3');

        $postPhotoOne = UploadedFile::fake()->image('test_img.jpg');
        $postOnePhotoFilename = 'posts/' . time() . $postPhotoOne->getClientOriginalName();
        $postPhotoTwo = UploadedFile::fake()->image('test_img.png');
        $postTwoPhotoFilename = 'posts/' . time() . $postPhotoTwo->getClientOriginalName();

        Storage::disk('s3')->put($postOnePhotoFilename, '');
        Storage::disk('s3')->put($postTwoPhotoFilename, '');

        Post::factory()
            ->count(2)
            ->for($subjectUser)
            ->state(new Sequence(
                ['photo_link' => 'https://hart-looped.s3.amazonaws.com/posts/' . $postOnePhotoFilename, 'photo_filename' => $postOnePhotoFilename],
                ['photo_link' => 'https://hart-looped.s3.amazonaws.com/posts/1125432' . $postTwoPhotoFilename, 'photo_filename' => $postTwoPhotoFilename],

            ))
            ->create(
                [
                    'author_user_id' => $authorUser->id
                ]
            );

        $authorizedUsers = [$subjectUser, $authorUser];

        $responses = [];

        foreach ($authorizedUsers as $key => $authorizedUser) {

            /**@var mixed $authorizedUser */
            $response = $this
                ->actingAs($authorizedUser, 'api')
                ->deleteJson(
                    '/api/auth/posts/' . $subjectUser->posts[$key]->id . '/delete?user=' . $authorizedUser->id,
                    []
                );

            array_push($responses, $response->getStatusCode());
            array_push($responses, $response->getData()->msg);
        }
        $this->assertSame(
            [
                200,
                'post deleted',
                200,
                'post deleted'
            ],
            $responses
        );
        $subjectUser->refresh();

        $this->assertEquals(0, $subjectUser->posts->count());

        $this->assertFalse(Storage::disk('s3')->exists($postOnePhotoFilename));
        $this->assertFalse(Storage::disk('s3')->exists($postTwoPhotoFilename));
    }

    /** @test */
    public function it_makes_sure_unauthorized_users_cannot_delete_a_post()
    {
        $subjectUser = User::factory()->create(['id' => 1]);
        $authorUser = User::factory()->create(['id' => 2]);
        $currentUser = User::factory()->create(['id' => 3]);

        Post::factory()->for($subjectUser)->create(['author_user_id' => $authorUser->id]);

        /**@var mixed $currentUser */
        $response = $this
            ->actingAs($currentUser, 'api')
            ->deleteJson(
                '/api/auth/posts/' . $subjectUser->posts[0]->id . '/delete?user=' . $currentUser->id,
                []
            );

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'error' => 'User not allowed to delete post'
            ]
        );
    }

    /** @test */
    public function it_returns_not_found_if_post_to_delete_does_not_exist()
    {
        $nonExistentPostId = 5;

        $subjectUser = User::factory()
            ->create();

        Post::factory()
            ->for($subjectUser)
            ->create();

        /**@var mixed $subjectUser */
        $response = $this
            ->actingAs($subjectUser, 'api')
            ->deleteJson(
                '/api/auth/posts/' . $nonExistentPostId . '/delete?user=' . $subjectUser->id,
                []
            );

        $response->assertStatus(404);
        $response->assertJsonFragment(
            [
                'error' => 'Post not found'
            ]
        );
    }

    /** @test */
    public function it_returns_posts_for_a_users_profile()
    {
        $subjectUser = User::factory()->has(Profile::factory()->fullMedia())->create();

        $users = User::factory()
            ->count(9)
            ->has(Profile::factory()->fullMedia())
            ->create();

        foreach ($users as $key => $user) {

            Post::factory()
                ->for($subjectUser)
                ->state(
                    [
                        'author_user_id' => $user->id,
                        'created_at' => round($key % 2 === 0) ?
                            '2021-07-11 20:04:39' : '2021-06-11 20:04:39',
                    ]
                )
                ->create();
        }

        foreach ($subjectUser->posts as $key => $post) {
            $mainComment = Comment::factory()
                ->for($post)
                ->create(
                    [
                        'user_id' => $post->author_user_id,
                        'reply_to_comment_id' => null
                    ]
                );
            Comment::factory()
                ->for($post)
                ->create(
                    [
                        'user_id' => $post->author_user_id,
                        'reply_to_comment_id' => $mainComment->id
                    ]
                );
        }

        $lastPost = $subjectUser->posts->last()->id;
        $responses = [];

        for ($i = 0; $i < 3; $i++) {
            if ($i > 0) {
                $lastPost = $lastPost - 3;
            }

            /**@var mixed $subjectUser */
            $response = $this
                ->actingAs($subjectUser, 'api')
                ->getJson(
                    '/api/auth/posts?subjectId=' .
                        $subjectUser->id .
                        '&lastPost=' .
                        $lastPost .
                        '&filters=',
                    []
                );

            $response->assertStatus(200);
            array_push($responses, $response->getData()->posts);
        }

        $postIDsOrder = [];
        foreach ($responses as $index => $response) {
            array_push($postIDsOrder, ...array_map(fn ($column) => $column->id, $response));
        }

        $this->assertEquals(
            13,
            $responses[0][1]->post_comments[0]->id
        );
        $this->assertEquals(
            13,
            intval(
                $responses[0][1]
                    ->post_comments[0]
                    ->reply_comments[0]
                    ->reply_to_comment_id
            )
        );
        $this->assertSame([8, 7, 6, 5, 4, 3, 2, 1], $postIDsOrder);
    }
}
