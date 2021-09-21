<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\FollowSuggestion;
use App\Models\User;
use App\Models\Profile;
use App\Models\Stat;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class FollowSuggestionControllerTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    protected $currentUser;

    public function setUp(): void
    {
        parent::setUp();
        $this->currentUser = User::factory()
            ->create(
                [
                    'id' => 1
                ]
            );

        Profile::factory()
            ->for($this->currentUser)
            ->fullMedia()
            ->create(
                [
                    'user_id' => $this->currentUser->id
                ]
            );
    }
    /** @test */
    public function it_aggregates_and_retrieves_follow_suggestions_for_the_current_user()
    {

        $users = User::factory()->count(10)->create();

        $currentUserStatInfo =   collect([
            'id' => $this->currentUser->id,
            'name' => $this->currentUser->full_name,
            'timestamp' => time(),
        ]);

        foreach ($users as $user) {

            Profile::factory()
                ->for($user)
                ->fullMedia()
                ->create(
                    [
                        'user_id' => $user->id
                    ]
                );
        }

        $offset = floor($users->count() / 2);

        $followingUsers = collect([]);
        $currentUserNetwork = collect([]);

        foreach ($users->slice(0, $offset)->values() as $key => $user) {

            $potentialProspect = collect($users->slice($offset)->values()[$key]);

            $potentialProspectInfo = $potentialProspect
                ->filter(function ($item, $key) {
                    if (in_array($key, ['id', 'full_name'])) {
                        return $item;
                    }
                })
                ->merge(['timestamp' => now()->timestamp]);

            $potentialProspectInfo['name'] = $potentialProspectInfo['full_name'];
            unset($potentialProspectInfo['full_name']);

            $following = [
                $currentUserStatInfo['id'] => $currentUserStatInfo,
                $potentialProspectInfo['id'] => $potentialProspectInfo,
            ];

            Stat::factory()->for($user)
                ->create([
                    'profile_id' => $user->profile->id,
                    'user_id' => $user->id,
                    'following_count' => 2,
                    'followers_count' => 2,
                    'name' => $user->full_name,
                    'following' => $following,
                    'followers' =>
                    [
                        $currentUserStatInfo['id'] =>
                        $currentUserStatInfo
                    ]
                ]);

            $followingUsers->push($user);
            $currentUserNetwork[$user->id] =
                [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'timestamp' => now()->timestamp
                ];
        }

        Stat::factory()
            ->for($this->currentUser)
            ->create(
                [
                    'profile_id' => $this->currentUser->profile->id,
                    'user_id' => $this->currentUser->id,
                    'following_count' => $currentUserNetwork->count(),
                    'followers_count' => 2,
                    'name' => $this->currentUser->full_name,
                    'following' => $currentUserNetwork,
                    'followers' => $currentUserNetwork->slice(0, 2),
                ]
            );



        $potentialProspects = collect($users->slice($offset)->values());

        $currentUserFollowing = $currentUserNetwork->values();


        foreach ($potentialProspects as $key => $potentialProspect) {

            Profile::factory()
                ->for($potentialProspect)
                ->fullMedia()
                ->create(
                    [
                        'user_id' => $potentialProspect->id
                    ]
                );

            $potentialProspectData = $currentUserNetwork[$currentUserFollowing[$key]['id']];

            $potentialProspectNetwork = [
                $potentialProspectData['id'] => $potentialProspectData,
            ];
            Stat::factory()->for($potentialProspect)->create(
                [
                    'profile_id' => $potentialProspect->profile->id,
                    'user_id' => $potentialProspect->id,
                    'following_count' => 1,
                    'followers_count' => 1,
                    'name' => $potentialProspect->full_name,
                    'following' =>  $key % 2 === 0 ? $potentialProspectNetwork : NULL,
                    'followers' => $potentialProspectNetwork,
                ]
            );
        }

        $perPage = 2;
        $total = $potentialProspects
            ->filter(function (User $user, $key) {

                if (!is_null($user->stat->following)) {
                    return $user;
                }
            })->count();
        $numOfPages = ceil($total / $perPage);

        $currentPage = 0;
        $last = NULL;
        $actualProspectUserIDs = collect([]);

        while ($currentPage < $numOfPages) {
            $response = $this->actingAs($this->currentUser, 'api')
                ->getJson(
                    '/api/auth/follow-suggestions/' .
                        $this->currentUser->id .
                        '/show?last=' .
                        $last . '&end=0',
                    []
                );

            $followSuggestions = $response->getData()->follow_suggestions;

            $last = count($followSuggestions) - 1;
            $last = $followSuggestions[$last]->id;

            $response->assertStatus(200);

            $actualProspectUserIDs->push(...array_map(
                fn ($followSuggestion) => $followSuggestion->prospect_user_id,
                $response->getData()->follow_suggestions
            ));

            $currentPage++;
        }
        $this->assertSame([7, 9, 11], $actualProspectUserIDs->toArray());
    }

    /** @test */
    public function it_retrieves_preexisting_follow_suggestions_that_are_not_rejected_for_the_current_user()
    {
        $this->assertEquals(1, 1);

        $users = User::factory()->count(3)->create();
        $userIDs = $users->pluck('id');

        foreach ($users as $user) {
            Profile::factory()
                ->for($user)
                ->fullMedia()
                ->create(
                    [
                        'user_id' => $user->id
                    ]
                );
        }

        foreach ($userIDs as $key => $userID) {

            $dynamicColumns = $key % 2 !== 0 ? [
                'rejected_at' => now()->timestamp - 200,
                'rejected' => 1,
                'suggest' => 0,
            ] : [];

            FollowSuggestion::factory()->create(
                array_merge(
                    [
                        'user_id' => $this->currentUser->id,
                        'profile_id' => $users[$key]->profile->id,
                        'prospect_user_id' => $userID,
                        'unique_identifier' => $userID . '_' . $this->currentUser->id,

                    ],
                    $dynamicColumns
                )
            );
        }

        $last = NULL;

        $response = $this->actingAs($this->currentUser, 'api')
            ->getJson(
                '/api/auth/follow-suggestions/' .
                    $this->currentUser->id .
                    '/show?last=' .
                    $last . '&end=0',
                []
            );

        $followSuggestions = json_decode(json_encode($response->getData()->follow_suggestions), true);

        $actualIDs = array_merge(
            [
                'prospects' => array_column($followSuggestions, 'prospect_user_id')
            ],
            [
                'current_user' => array_column($followSuggestions, 'user_id')
            ]
        );

        $response->assertStatus(200);

        $this->assertSame([0, 0], array_column($followSuggestions, 'rejected'));
        $this->assertSame([1, 1], $actualIDs['current_user']);
        $this->assertSame([2, 4], $actualIDs['prospects']);
    }

    /** @test */
    public function it_will_look_for_new_follow_suggestions_and_return_them()
    {
        $currentUserNetwork = [
            2 => [
                'id' => 2,
                'name' => 'john smith',
                'timestamp' => now()->timestamp,
            ]
        ];

        Stat::factory()
            ->for($this->currentUser)
            ->create(
                [
                    'profile_id' => $this->currentUser->profile->id,
                    'user_id' => $this->currentUser->id,
                    'following_count' => 1,
                    'followers_count' => 1,
                    'following' => $currentUserNetwork,
                    'followers' => $currentUserNetwork,
                ]
            );

        $followingUserNetwork = [
            3 => [
                'id' => 3,
                'name' => 'jayne doe',
                'timestamp' => now()->timestamp
            ],
            4 => [
                'id' =>  4,
                'name' => 'john doe',
                'timestamp' => now()->timestamp
            ],
        ];

        $followingUserNetwork = array_merge(
            $followingUserNetwork,
            [
                [
                    $this->currentUser->id => 'id',
                    'id' => $this->currentUser->id,
                    'name' => $this->currentUser->full_name,
                    'timestamp' => now()->timestamp,
                ]
            ]
        );

        User::factory()
            ->has(Profile::factory()->fullMedia()
                ->state(
                    [
                        'id' => array_key_first($currentUserNetwork),
                        'user_id' => array_key_first($currentUserNetwork)
                    ]
                ))->has(Stat::factory()
                ->state(
                    [
                        'profile_id' => array_key_first($currentUserNetwork),
                        'user_id' => array_key_first($currentUserNetwork),
                        'following_count' => count($followingUserNetwork) + 1,
                        'followers_count' => count($followingUserNetwork),
                        'following' => array_combine(
                            array_map(fn ($user) => $user['id'], $followingUserNetwork),
                            $followingUserNetwork
                        ),
                        'followers' => $followingUserNetwork,
                    ]
                ))
            ->create(
                [
                    'id' => array_key_first($currentUserNetwork),
                    'full_name' => $currentUserNetwork[array_key_first($currentUserNetwork)]['name'],
                    'first_name' => 'john',
                    'last_name' => 'smith',
                ]
            );

        $prospects = collect([]);
        foreach (array_slice($followingUserNetwork, 0, 2) as $key => $user) {
            $prospect = User::factory()
                ->has(Profile::factory()->fullMedia()
                    ->state(
                        [
                            'user_id' => $user['id'],
                            'id' => $user['id'],
                        ]
                    ))
                ->has(Stat::factory()->state(
                    [
                        'profile_id' => $user['id'],
                        'user_id' => $user['id'],
                        'name' => $user['name'],
                        'following_count' => 1,
                        'followers_count' => 1,
                        'following' => $currentUserNetwork,
                        'followers' => $currentUserNetwork,
                    ]
                ))
                ->create(
                    [
                        'id' => $user['id'],
                        'full_name' => $user['name'],
                        'first_name' => str_split($user['name'])[0],
                        'last_name' => str_split($user['name'])[1],
                    ]
                );
            $prospects->push($prospect);
        }

        FollowSuggestion::factory()
            ->create(
                [
                    'user_id' => $this->currentUser->id,
                    'profile_id' => $this->currentUser->profile->id,
                    'prospect_user_id' => 20,
                    'unique_identifier' => '20_' . $this->currentUser->id,
                    'rejected' => 0,
                    'suggest' => 1,
                    'rejected_at' => now()->timestamp - 200,
                ]
            );

        $last = $this->currentUser->recipientFollowSuggestion->first()->id;

        $response = $this->actingAs($this->currentUser, 'api')->getJson(
            '/api/auth/follow-suggestions/' .
                $this->currentUser->id .
                '/show?last=' .
                $last . '&end=1',
            []
        );

        $actualFollowSuggestions = array_map(fn ($suggestion) => $suggestion->prospect_user_id, $response->getData()->follow_suggestions);

        $this->assertSame($prospects->pluck('id')->toArray(), $actualFollowSuggestions);
        $this->assertCount(2, $response->getData()->follow_suggestions);
    }

    /** @test */
    public function it_tries_to_aggregate_suggestions_and_fails_due_to_lack_of_social_network()
    {

        $followUserStatInfo = [2 => [
            'id' => 2,
            'name' => 'john smith',
            'timestamp' => now()->timestamp,
        ]];

        Stat::factory()->for($this->currentUser)->create([
            'profile_id' => $this->currentUser->id,
            'user_id' => $this->currentUser->id,
            'name' => $this->currentUser->full_name,
            'following_count' => 1,
            'followers_count' => 1,
            'following' => $followUserStatInfo,
            'followers' => $followUserStatInfo,
        ]);

        [$firstName, $lastName] = explode(' ', $followUserStatInfo[array_key_first($followUserStatInfo)]['name']);



        $prospects = collect([]);

        foreach ($prospectIDSs = [3, 4] as $key => $prospectID) {



            $networkFollowing = $key % 2 === 0 ?
                [5 => [
                    'id' => 5,
                    'name' => $this->faker->name(),
                    'timestamp' => now()->timestamp,
                ]] : [6 => [
                    'id' => 6,
                    'name' => $this->faker->name(),
                    'timestamp' => now()->timestamp,
                ]];


            [$firstName, $lastName] = explode(' ', $this->faker->name());
            $prospect = User::factory()
                ->has(Profile::factory()
                    ->fullMedia()
                    ->state(
                        [
                            'id' => $prospectID,
                            'user_id' => $prospectID

                        ]
                    ))->has(Stat::factory()
                    ->state(
                        [
                            'user_id' => $prospectID,
                            'profile_id' => $prospectID,
                            'name' => $firstName . ' ' . $lastName,
                            'following' => $networkFollowing,
                            'followers' =>
                            [2 => [
                                'id' => $followUserStatInfo[array_key_first($followUserStatInfo)]['id'],
                                'name' => $followUserStatInfo[array_key_first($followUserStatInfo)]['name'],
                                'timestamp' => now()->timestamp,
                            ]],
                            'following_count' => 1,
                            'followers_count' => 2,
                        ]
                    ))->create(
                    [
                        'id' => $prospectID,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'full_name' => $firstName . ' ' . $lastName,
                    ]
                );
            $prospects->push($prospect);
        }

        $prospectStatInfo =  [
            $prospects[0]->id => [
                'id' => $prospects[0]->id,
                'name' => $prospects[0]->full_name,
                'timestamp' => now()->timestamp,
            ],
            $prospects[1]->id => [
                'id' => $prospects[1]->id,
                'name' => $prospects[1]->full_name,
                'timestamp' => now()->timestamp,

            ]
        ];

        $followingUserFollowers = array_slice($prospectStatInfo, 0, 1)[0];

        User::factory()
            ->has(Profile::factory()->fullMedia()
                ->state(
                    [
                        'id' => array_key_first($followUserStatInfo),
                    ]
                ))
            ->has(Stat::factory()
                ->state(
                    [
                        'user_id' => array_key_first($followUserStatInfo),
                        'profile_id' => array_key_first($followUserStatInfo),
                        'name' => $firstName . ' ' . $lastName,
                        'following' => $prospectStatInfo,
                        'following_count' => count($prospectStatInfo),
                        'followers_count' => 1,
                        'followers' => [
                            $followingUserFollowers['id'] => [
                                $followingUserFollowers
                            ]
                        ],
                        'followers' => [$prospects[0]->id => [
                            'id' => $prospects[0]->id,
                            'name' => $prospects[0]->full_name,
                            'timestamp' => now()->timestamp,
                        ]],
                    ]
                ))
            ->create(
                [
                    'id' => array_key_first($followUserStatInfo),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $firstName . ' ' . $lastName,
                ]
            );


        $response = $this->actingAs($this->currentUser, 'api')
            ->getJson(
                '/api/auth/follow-suggestions/' .
                    $this->currentUser->id .
                    '/show?last=' .
                    null . '&end=1',
                []
            );

        $response->assertStatus(200);
        $response->assertJsonFragment(['msg' => 'Follow suggestions retrieved']);

        $this->assertCount(0, $response->getData()->follow_suggestions);
        $this->assertEquals(0, $response->getData()->total);
    }
}
