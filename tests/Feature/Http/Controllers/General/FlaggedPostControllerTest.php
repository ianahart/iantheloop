<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class FlaggedPostControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $reasons;
    protected $postedByUser;
    protected $userFlagging;

    public function setUp(): void
    {
        parent::setUp();
        [$this->postedByUser, $this->userFlagging] = User::factory()
            ->count(2)
            ->create();

        $this->reasons = array_map(function ($k, $v) {

            return ['id' => $k + 1, 'reasonText' => $v, 'selected' => true];
        }, array_keys(['Violence', 'Nudity', 'Harassment']), ['Violence', 'Nudity', 'Harassment']);
    }

    /** @test */
    public function it_stores_a_flagged_post_if_user_has_not_already_flagged()
    {
        Post::factory()
            ->for($this->postedByUser)
            ->create(
                [
                    'author_user_id' => $this->postedByUser->id,
                    'subject_user_id' => $this->postedByUser->id
                ]
            );

        $responses = [];

        for ($i = 0; $i < 2; $i++) {
            $response = $this
                ->actingAs($this->userFlagging, 'sanctum')
                ->postJson(
                    '/api/auth/flagged-posts/store',
                    [
                        'post_id' => $this->postedByUser->posts[0]->id,
                        'user_id' => $this->userFlagging->id,
                        'reasons' => $this->reasons,
                    ]
                );
            $responses[] = ['status' => $response->getStatusCode(), 'msg' => $response->getData()->msg];
        }

        $this->userFlagging->refresh();

        $this->assertEquals(200, $responses[0]['status']);
        $this->assertEquals(409, $responses[1]['status']);

        $this->assertCount(1, $this->postedByUser->posts[0]->flaggedPosts, true);
    }
}
