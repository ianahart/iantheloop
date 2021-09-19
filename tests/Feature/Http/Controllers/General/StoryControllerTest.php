<?php

namespace Tests\Feature\Http\Controllers\General;

use App\Http\Controllers\General\StoryController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;
use Illuminate\Http\UploadedFile;
use App\Http\Requests\StoryPostRequest;
use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Support\Facades\Event;
use App\Jobs\ProcessStoryPhoto;
use App\Events\StoryPhotoProcessed;
use App\Models\Profile;
use App\Models\User;
use App\Models\Stat;
use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class StoryControllerTest extends TestCase
{
    use RefreshDatabase;
    use AdditionalAssertions;
    use WithFaker;

    protected $currentUser;
    protected $photoFile;


    public function setUp(): void
    {
        parent::setUp();

        $this->photoFile = new UploadedFile(resource_path('assets/test_image.jpg'), 'test_image.jpg', 'image/jpeg', null, true);

        $this->currentUser = User::factory()
            ->create(
                [
                    'id' => 1,
                    'profile_created' => 1,
                ]
            );

        $profile = Profile::factory()
            ->for($this->currentUser)
            ->fullMedia()
            ->create(
                [
                    'user_id' => $this->currentUser->id,
                    'id' => 1,
                ]
            );

        Story::factory()
            ->count(2)
            ->for($profile)
            ->photoStory()
            ->create(
                [
                    'user_id' => $this->currentUser->id,
                    'profile_id' => $profile->id,
                    'story_user_id' => $this->currentUser->id,
                    'created_at_unix' => now()->timestamp,
                    'expire_in_unix' => now()->timestamp + 86400,
                    'created_at' => now(),
                    'story_type' => 'photo',
                ]
            );
    }

    /** @test */
    public function it_saves_a_current_users_story()
    {
        $this->assertActionUsesFormRequest(StoryController::class, 'store', StoryPostRequest::class);

        $textData = json_encode([
            'user_id' => $this->currentUser->id,
            'story_type' => 'photo',
            'text' => NULL,
            'alignment' => 'center',
            'color' => 'black',
            'duration' => [
                'name' => '10s',
                'value' => 10000,
            ],
            'font_size' => '12px',
            'background' => 'linear-gradient(90deg, #833ab4 0%, #91fd1d 50%, #fcb045 100%)'
        ]);

        $body = [
            'data' => $textData,
            'file' => $this->photoFile,
        ];

        Storage::fake('s3');
        Storage::fake();
        Bus::fake();
        Event::fake();

        Event::dispatch(StoryPhotoProcessed::class);

        $response = $this->actingAs($this->currentUser, 'api')->postJson(
            '/api/auth/stories/create',
            $body,
        );

        $response->assertStatus(201);
        Bus::assertDispatched(
            function (ProcessStoryPhoto $job) use ($textData) {
                return intval($job->getData()['user_id']) === intval(json_decode($textData, true)['user_id']);
            }
        );
    }

    /** @test */
    public function it_dispatches_story_event_to_broadcast_to_users()
    {

        $followingUser = User::factory()
            ->has(Profile::factory()->fullMedia())
            ->create(
                [
                    'id' => 2
                ]
            );

        $users = [$this->currentUser, $followingUser];

        foreach ($users as $key => $user) {

            $index = $key == 0 ? $key + 1 : $key - 1;

            Stat::factory()
                ->for($user)
                ->create(
                    [
                        'profile_id' => $user->profile->id,
                        'user_id' => $user->id,
                        'following_count' => 1,
                        'followers_count' => 1,
                        'name' => $user->full_name,
                        'following' =>
                        [
                            $users[$index]->id => [
                                'id' => $users[$index]->id,
                                'name' => $users[$index]->full_name,
                                'timestamp' => time(),
                            ]
                        ],
                        'followers' =>
                        [
                            $users[$index]->id => [
                                'id' => $users[$index]->id,
                                'name' => $users[$index]->full_name,
                                'timestamp' => time(),
                            ]
                        ]
                    ]
                );
        }

        Event::fake();

        $related = [
            'full_name' => $this->currentUser->full_name,
            'profile_picture' => $this->currentUser->profile->profile_picture,
            'followers' => array_keys($this->currentUser->stat->followers),
        ];

        StoryPhotoProcessed::dispatch($this->currentUser->subjectStory, $related);

        Event::assertDispatched(
            function (StoryPhotoProcessed $event) use ($related) {
                return $event->story->id === $this->currentUser->subjectStory->id &&
                    $related['followers'][0] === array_keys($this->currentUser->stat->followers)[0];
            }
        );
    }

    /** @test */
    public function it_gets_base_profile_data_to_display_the_story_buttons()
    {
        $users = User::factory()->count(5)->create();

        $userNetwork = [
            $this->currentUser->id => [
                'id' => $this->currentUser->id,
                'name' => $this->currentUser->full_name,
                'timestamp' => time(),
            ]
        ];

        $currentUserNetwork = collect([]);

        foreach ($users as $key => $user) {

            $currentUserNetwork->push(
                ...[
                    $user->id => [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'timestamp' => time(),
                    ]
                ]
            );

            Profile::factory()
                ->for($user)
                ->fullMedia()
                ->create(
                    [
                        'user_id' => $user->id
                    ]
                );

            $userStat =
                [
                    'followers_count' => 1,
                    'following_count' => 1,
                    'profile_id' => $user->profile->id,
                    'name' => $user->full_name,
                    'following' => $userNetwork,
                    'followers' => $userNetwork,
                ];

            Stat::factory()
                ->for($user)
                ->create($userStat);

            Story::factory()->count(2)
                ->for($user)
                ->create(
                    [
                        'user_id' => $user->id,
                        'profile_id' => $user->profile->id,
                        'created_at_unix' => now()->timestamp,
                        'expire_in_unix' => now()->timestamp + 86400,

                    ]
                );
        }

        $keys = $currentUserNetwork->map(fn ($arr) => $arr['id']);
        $currentUserNetwork = $keys->combine($currentUserNetwork);

        Stat::factory()->for($this->currentUser)
            ->create(
                [
                    'profile_id' => $this->currentUser->profile->id,
                    'followers_count' => count($currentUserNetwork),
                    'following_count' => count($currentUserNetwork),
                    'name' => $this->currentUser->full_name,
                    'following' => $currentUserNetwork,
                    'followers' => $currentUserNetwork,
                ]
            );

        $numOfPages = ceil(count($this->currentUser->stat->following) / 3);
        $page = 1;
        $actualBaseStoryIDS = collect([]);
        $actualPages = collect([]);
        while ($page <= $numOfPages) {
            $response = $this->actingAs($this->currentUser, 'api')
                ->getJson(
                    '/api/auth/stories/index?page=' . $page,
                    []
                );
            $response->assertStatus(200);
            $IDS = array_map(fn ($baseStory) => json_decode(json_encode($baseStory), true)['id'], $response->getData()->stories->data);

            $actualBaseStoryIDS->push(...$IDS);
            $actualPages->push($response->getData()->stories->current_page);
            $page++;
        }

        $expectedBaseStoryIDS = $currentUserNetwork->keys()->reverse()->values();

        $this->assertEquals($expectedBaseStoryIDS, $actualBaseStoryIDS->values());
        $this->assertEquals(collect([1, 2]), $actualPages);
    }

    /** @test */
    public function get_the_count_of_stories_posted_by_user_in_the_past_day()
    {

        $response = $this->actingAs($this->currentUser, 'api')
            ->getJson(
                '/api/auth/stories/' . $this->currentUser->id . '/count/show',
                []
            );
        $response->assertStatus(200);
        $this->assertEquals(
            $this->currentUser->subjectStories->count(),
            $response->getData()->user_stories_count
        );
    }

    /** @test */
    public function it_retrieves_all_of_a_specified_user_stories()
    {
        $user = User::factory()
            ->create(
                [
                    'id' => 2
                ]
            );

        Profile::factory()
            ->for($user)
            ->fullMedia()
            ->create(
                [
                    'user_id' => $user->id
                ]
            );

        $instances = [1, 2, 3, 4];
        foreach ($instances as $key => $value) {

            $time =
                $value === $instances[0] || $value === count($instances) ? [
                    'created_at_unix' => now()->timestamp,
                    'expire_in_unix' => now()->timestamp + 86400,

                ] : [
                    'created_at_unix' => now()->timestamp,
                    'expire_in_unix' => now()->timestamp,

                ];

            $storyFactory =  $key % 2 === 0 ? Story::factory()->for($user)->photoStory() : Story::factory()->for($user);

            $storyFactory
                ->create(
                    array_merge([
                        'user_id' => $user->id,
                        'profile_id' => $user->profile->id,
                        'story_user_id' => $user->id,
                        'story_type' => 'photo',
                    ], $time)
                );
        }

        Storage::fake('s3');
        /** @var mixed $user */
        $response = $this->actingAs($user, 'api')
            ->getJson(
                '/api/auth/stories/' .
                    $user->id . '/show',
                []
            );

        $response->assertStatus(200);
        $this->assertCount(2, $response->getData()->stories);

        [$a, $b] = array_map(
            fn ($story) => json_decode(json_encode($story), true),
            $response->getData()->stories
        );

        $this->assertContains('stories/test_image.jpg', array_diff($a, $b));
    }

    /** @test */
    public function it_deletes_a_users_story()
    {
        Storage::fake('s3');

        $response = $this->actingAs($this->currentUser, 'api')
            ->deleteJson(
                '/api/auth/stories/' .
                    $this->currentUser->subjectStory->id .
                    '/delete?userId=' .
                    $this->currentUser->id,
                []
            );

        $response->assertStatus(200);
        $this->currentUser->refresh();

        $this->assertCount(1, $this->currentUser->subjectStories);
    }


    /** @test */
    public function it_does_not_let_current_user_delete_someone_elses_story()
    {
        Storage::fake('s3');

        $otherUser = User::factory()
            ->create(
                [
                    'id' => 2
                ]
            );

        Profile::factory()->for($otherUser)
            ->fullMedia()
            ->create(
                [
                    'user_id' => $otherUser->id
                ]
            );

        Story::factory()
            ->for($otherUser)
            ->create(
                [
                    'user_id' => $otherUser->id,
                    'story_user_id' => $otherUser->id,
                    'profile_id' => $otherUser->profile->id,
                    'created_at_unix' => now()->timestamp,
                    'expire_in_unix' => now()->timestamp,
                ]
            );

        $response = $this->actingAs($this->currentUser, 'api')
            ->deleteJson(
                '/api/auth/stories/' .
                    $otherUser->subjectStory->id .
                    '/delete?userId=' .
                    $otherUser->id,
                []
            );
        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'error' => 'Forbidden to delete a story that is not yours.'
            ]
        );
    }
}
