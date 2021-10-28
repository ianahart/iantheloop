<?php

namespace Tests\Feature\Http\Controllers\General;

use App\Http\Controllers\General\SettingController;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use JMac\Testing\Traits\AdditionalAssertions;

use App\Models\User;
use App\Models\Stat;
use App\Models\Privacy;
use App\Models\Setting;
use App\Models\Profile;





use Tests\TestCase;

class SettingControllerTest extends TestCase
{

    use RefreshDatabase;
    use AdditionalAssertions;
    use WithFaker;

    protected User $currentUser;
    protected $currentUserNetwork;
    protected array $staticNames;

    public function setUp(): void
    {
        parent::setUp();

        $this->currentUser = User::factory()
            ->has(
                Profile::factory()->fullMedia()
            )
            ->has(
                Setting::factory()
            )
            ->create(
                [
                    'id' => 1
                ]
            );

        $this->currentUserNetwork = User::factory()
            ->has(
                Profile::factory()->fullMedia()
            )
            ->count(10)
            ->create();

        $this->staticNames = [
            '2' =>
            [
                'first_name' => 'Allen',
                'last_name' => 'Smith',
                'full_name' => 'Allen Smith'
            ],
            '4' =>
            [
                'first_name' => 'Jane',
                'last_name' => 'Amerally',
                'full_name' => 'Jane Amerally'
            ],
            '8' =>
            [
                'first_name' => 'Andrew',
                'last_name' => 'Arndonall',
                'full_name' => 'Andrew Arndonall'
            ]
        ];

        $following = [];
        $followers = [];

        foreach ($this->currentUserNetwork as $key => $user) {

            if (in_array($user->id, array_keys($this->staticNames))) {

                $user->full_name = $this->staticNames[strval($user->id)]['full_name'];
                $user->first_name = $this->staticNames[strval($user->id)]['first_name'];
                $user->last_name = $this->staticNames[strval($user->id)]['last_name'];
                $user->save();
            }

            if ($key % 2 === 0) {
                $following[$user->id] = [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'timestamp' => time(),
                ];
            } else {
                $followers[$user->id] = [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'timestamp' => time(),
                ];
            }

            Stat::factory()->for($user)
                ->create([
                    'profile_id' => $user->profile->id,
                    'user_id' => $user->id,
                    'following_count' => 1,
                    'followers_count' => 1,
                    'name' => $user->full_name,
                    'following' => $key % 2 === 0 ? NULL :
                        [
                            $this->currentUser->id => [
                                'id' => $this->currentUser->id,
                                'name' => $this->currentUser->full_name,
                                'timestamp' => time(),
                            ]
                        ],
                    'followers' => $key % 2 === 0 ?
                        [
                            $this->currentUser->id => [
                                'id' => $this->currentUser->id,
                                'name' => $this->currentUser->full_name,
                                'timestamp' => time(),
                            ]
                        ] : NULL,
                ]);
        }

        Stat::factory()->for($this->currentUser)
            ->create([
                'profile_id' => $this->currentUser->profile->id,
                'user_id' => $this->currentUser->id,
                'following_count' => count($following),
                'followers_count' => count($followers),
                'name' => $this->currentUser->full_name,
                'following' => $following,
                'followers' => $followers,
            ]);
    }
    /** @test */
    public function it_creates_a_users_settings()
    {

        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->postJson('/api/auth/settings/create', ['current_user_id' => $this->currentUser->id]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['msg' => 'Success']);
    }

    /** @test */
    public function it_searches_for_users_in_the_current_users_network_who_are_not_blocked_yet_by_type()
    {

        $blockedTypeProfiles = [2, 4, 10];

        foreach ($blockedTypeProfiles as $blockedTypeProfile) {
            Privacy::factory()
                ->create(
                    [
                        'blocked_by_user_id' => $this->currentUser->id,
                        'blocked_profile' => 1,
                        'profile_id' => $this->currentUser->profile->id,
                        'stat_id' => $this->currentUser->stat->id,
                        'setting_id' => $this->currentUser->setting->id,
                        'blocked_user_id' => $this->currentUser->stat->following[strval($blockedTypeProfile)]['id'],
                    ]
                );
        }


        $response = $this->actingAs($this->currentUser, 'api')->postJson('/api/auth/settings/block/search', [
            'current_user_id' => $this->currentUser->id,
            'type' => 'blocked_profile',
            'value' => 'all'
        ]);

        $searchResults = json_decode(json_encode($response->getData()), true)['searches']['data'];

        $blockedProfileByCurrentUser = $this->currentUser->blockedList
            ->pluck('blocked_user_id')
            ->toArray();

        [$expectedSearchResult] = array_values(
            array_diff(
                array_keys($this->staticNames),
                $blockedProfileByCurrentUser
            )
        );

        $actualSearchResult = NULL;
        foreach ($searchResults as $searchResult) {

            if ($searchResult['id'] === $expectedSearchResult) {
                $actualSearchResult = $searchResult['id'];
                break;
            }
        }

        $response->assertStatus(200);
        $this->assertEquals($expectedSearchResult, $actualSearchResult);
    }

    /** @test */
    public function it_blocks_a_user_by_type()
    {

        $userBlockedFromStories = $this->currentUserNetwork[4];

        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->postJson(
                '/api/auth/settings/block/store',
                [
                    'current_user_id' => $this->currentUser->id,
                    'profile_id' => $userBlockedFromStories->profile->id,
                    'profile_picture' => $userBlockedFromStories->profile->profile_picture,
                    'type' => 'blocked_stories',
                    'user_id' => $userBlockedFromStories->id,
                ]
            );

        $response->assertStatus(201);

        $this->currentUser->refresh();

        $this->assertTrue($this->currentUser->blockedList->first()->blocked_stories);
        $this->assertEquals($userBlockedFromStories->id, $this->currentUser->blockedList->first()->blocked_user_id);
    }

    /** @test */
    public function it_retrieves_the_users_that_the_current_user_has_blocked()
    {
        $blockTypes = ['7' => ['blocked_stories' => 1,], '8' => ['blocked_messages' => 1], '9' => ['blocked_profile' => 1]];

        $this->currentUserNetwork->filter(function ($item, $key) use ($blockTypes) {
            if (in_array($item->id, array_keys($blockTypes))) {
                return $item;
            }
        })
            ->each(function ($blockedUser, $key) use ($blockTypes) {

                Privacy::factory()
                    ->create(
                        array_merge(
                            [
                                'blocked_by_user_id' => $this->currentUser->id,
                                'profile_id' => $this->currentUser->profile->id,
                                'stat_id' => $this->currentUser->stat->id,
                                'setting_id' => $this->currentUser->setting->id,
                                'blocked_user_id' => $blockedUser->id,
                            ],
                            $blockTypes[$blockedUser->id]
                        )
                    );
            });

        $url = '/api/auth/settings/block/' . $this->currentUser->id . '/show';
        $numOfRequests = ceil($this->currentUser->blockedList->count() / 2);
        $currentRequest = 0;

        $data = [];

        while ($currentRequest < $numOfRequests) {

            $response = $this
                ->actingAs($this->currentUser, 'api')
                ->getJson($url, []);

            $url = $response->getData()->blocked_users->next_page_url;
            array_push($data, ...json_decode(json_encode($response->getData()->blocked_users), true)['data']);

            $currentRequest++;
        }

        $actualBlockedUserIds = array_reverse(
            array_map(fn ($blockedUser) => $blockedUser['blocked_by_list']['blocked_user_id'], $data)
        );

        $blockedByUserIds = array_map(fn ($blockedUser) => $blockedUser['blocked_by_list']['blocked_by_user_id'], $data);

        $this->assertSame(array_keys($blockTypes), $actualBlockedUserIds);
        $this->assertSame([$this->currentUser->id, $this->currentUser->id, $this->currentUser->id], $blockedByUserIds);
    }

    /** @test */
    public function it_updates_a_blocked_user_by_specified_type_of_block()
    {
        $blockedUser = $this->currentUserNetwork[3];

        Privacy::factory()
            ->create(
                [
                    'blocked_by_user_id' => $this->currentUser->id,
                    'blocked_profile' => 1,
                    'profile_id' => $this->currentUser->profile->id,
                    'stat_id' => $this->currentUser->stat->id,
                    'setting_id' => $this->currentUser->setting->id,
                    'blocked_user_id' => $blockedUser->id,
                ]
            );

        $data = [
            'current_user_id' => $this->currentUser->id,
            'full_name' => $blockedUser->full_name,
            'id' => $blockedUser->id,
            'is_toggled' => true,
            'type' => 'blocked_messages',
            'profile' => [
                'id' => $blockedUser->profile->id,
                'profile_picture' => $blockedUser->profile->profile_picture,
                'user_id' => $blockedUser->id,
            ],
            'blocked_by_list' => [
                'blocked_by_user_id' => $this->currentUser->id,
                'blocked_date' => "Oct 11 21",
                'blocked_messages' => false,
                'blocked_profile' => true,
                'blocked_stories' => false,
                'blocked_user_id' => $blockedUser->id,
                'created_in_unix' => 1633973784,
                'privacy_id' => $this->currentUser->blockedList->first()->id,
                'setting_id' => $this->currentUser->setting->id,
            ]
        ];

        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->patchJson(
                '/api/auth/settings/block/' . $this->currentUser->blockedList->first()->id . ' /update',
                $data
            );

        $this->currentUser->refresh();

        $response->assertStatus(200);

        $this->assertEquals(true, $this->currentUser->blockedList[0]->blocked_messages);
        $response->assertJsonFragment(['types_blocked_count' => 2]);
    }

    /** @test */
    public function it_removes_the_block_on_the_specified_user_of_all_types()
    {
        $blockedUser = $this->currentUserNetwork[3];

        Privacy::factory()
            ->create(
                [
                    'blocked_by_user_id' => $this->currentUser->id,
                    'blocked_profile' => 1,
                    'blocked_messages' => 1,
                    'blocked_stories' => 1,
                    'profile_id' => $this->currentUser->profile->id,
                    'stat_id' => $this->currentUser->stat->id,
                    'setting_id' => $this->currentUser->setting->id,
                    'blocked_user_id' => $blockedUser->id,
                ]
            );

        $response = $this->actingAs($this->currentUser, 'api')
            ->deleteJson('/api/auth/settings/block/' .
                $this->currentUser->blockedList->first()->id .
                '/delete?userId=' .
                $this->currentUser->id, []);

        $this->currentUser->refresh();

        $response->assertStatus(200);
        $this->assertCount(0, $this->currentUser->blockedList);
    }

    /** @test */
    public function it_toggles_the_current_user_remember_me_setting_on_or_off()
    {

        $response = $this
            ->actingAs($this->currentUser, 'api')
            ->patchJson(
                '/api/auth/settings/remember-me/' . $this->currentUser->setting->id . '/update',
                [
                    'current_user_id' => $this->currentUser->id,
                    'remember_me' => true,
                ]
            );

        $this->currentUser->refresh();

        $response->assertStatus(200);
        $this->assertTrue($this->currentUser->setting->remember_me);
    }

    /** @test */
    public function it_returns_the_current_state_of_the_remember_me_setting_for_current_user()
    {

        $response = $this->actingAs($this->currentUser, 'api')
            ->getJson(
                '/api/auth/settings/security/' .
                    $this->currentUser->setting->id . '/show',
                []
            );

        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'security_settings' => ['remember_me', 'password_updated', 'password_updated_on']
            ]
        );
    }

    /** @test */
    public function it_updates_the_users_password()
    {
        $this->assertActionUsesFormRequest(SettingController::class, 'updatePassword', UpdatePasswordRequest::class);


        $user = User::factory()
            ->has(Setting::factory())
            ->create(
                [
                    'password' => Hash::make('Apple123%')
                ]
            );

        $data = [
            'form' =>
            [
                'old_password' => 'Apple123%',
                'password' => 'Apple1234%',
                'password_confirmation' => 'Apple1234%'
            ],
            'current_user_id' => $user->id,
        ];

        $token = JWTAuth::fromUser($user);

        /**@var mixed $user */
        $response = $this->actingAs($user, 'api')->withHeaders(
            [
                'Authorization' => 'Bearer ' . $token,
            ]
        )
            ->patchJson(
                '/api/auth/settings/password/' . $user->setting->id . '/update',


                $data,
            );

        $response->assertStatus(200);
        $response->assertJsonFragment(['data' => 'password updated']);

        $this->currentUser->refresh();
        $this->assertFalse(Hash::check($data['form']['password'], $this->currentUser->password));
    }

    /** @test */
    public function it_deletes_a_user_permanently()
    {
        Storage::fake('s3');
        $user = User::factory()
            ->has(Setting::factory())
            ->has(Profile::factory()->fullMedia())
            ->create(
                [
                    'password' => Hash::make('Apple123%')
                ]
            );

        $token = JWTAuth::fromUser($user);
        /**@var mixed $user */

        $response = $this->actingAs($user)->withHeaders(
            [
                'Authorization' => 'Bearer ' . $token,
            ]
        )->deleteJson('/api/auth/settings/account/' . $user->setting->id . '/delete', []);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
