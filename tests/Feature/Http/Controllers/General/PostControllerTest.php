<?php

namespace Tests\Feature\Http\Controllers\General;

use App\Http\Controllers\General\PostController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePostRequest;
use App\Models\User;
use App\Models\Post;
use App\Models\Profile;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    use AdditionalAssertions;
    use WithFaker;

    /** @test */
    public function it_stores_a_users_new_post()
    {
        $this->assertActionUsesFormRequest(PostController::class, 'store', StorePostRequest::class);

        $this->videoFile = new UploadedFile(resource_path('assets/test_vid.mp4'), 'test_vid.mp4', 'video/mp4', null, true);


        $newPost = [
            'subject_user_id' => 20,
            'author_user_id' => 7,
            'post_text' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.',

        ];

        $authorUser = User::factory()->has(Profile::factory())->create(['id' => 7]);
        $subjectUser = User::factory()->has(Profile::factory())->create(['id' => 20]);

        Storage::fake('s3');

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
}
