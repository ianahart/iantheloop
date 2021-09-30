<?php

namespace Tests\Feature\Http\Controllers\General;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use App\Http\Controllers\General\SearchController;
use App\Http\Requests\StoreSearchRequest;
use App\Models\Profile;
use App\Models\Search;
use App\Models\Stat;
use App\Models\User;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use AdditionalAssertions;

    protected $currentUser;

    public function setUp(): void
    {
        parent::setUp();
        $this->currentUser = User::factory()
            ->has(
                Profile::factory()
                    ->fullMedia()
                    ->state(
                        [
                            'company' => 'bilford mining'
                        ]
                    )
            )
            ->create(
                [
                    'id' => 1,
                    'full_name' => 'doug burns',
                    'first_name' => 'doug',
                    'last_name' => 'burns',
                ]
            );
    }

    /** @test */
    public function it_populates_search_results_from_user_input()
    {
        $this->assertActionUsesFormRequest(SearchController::class, 'search', StoreSearchRequest::class);

        $usersGenInfo = [
            ['id' => 2, 'full_name' => 'allen farington', 'company' => 'doomduck'],
            ['id' => 3, 'full_name' => 'john enty', 'company' => 'deer plug'],
            ['id' => 4, 'full_name' => 'enrique fort', 'company' => 'gold movies'],
            ['id' => 5, 'full_name' => 'jessica fran', 'company' => 'wingo'],
            ['id' => 6, 'full_name' => 'dalton fury', 'company' => 'furyswipes'],
            ['id' => 7, 'full_name' => 'sam tasse', 'company' => 'sweet enlighten'],
        ];

        $searchedUsers = collect([]);
        $currentUsersFollowing = [];

        foreach ($usersGenInfo as $key => $userGenInfo) {

            $searchedUser = User::factory()
                ->has(
                    Profile::factory()
                        ->fullMedia()
                        ->state(
                            [
                                'id' => $userGenInfo['id'],
                                'user_id' => $userGenInfo['id'],
                                'company' => $userGenInfo['company'],
                            ]
                        )
                )->has(Stat::factory()
                    ->state(
                        [
                            'profile_id' => $userGenInfo['id'],
                            'user_id' => $userGenInfo['id'],
                            'following_count' => 0,
                            'followers_count' => 1,
                            'name' => $userGenInfo['full_name'],
                            'following' => NULL,
                            'followers' => $key % 2 === 0 ?
                                [
                                    $this->currentUser->id => [
                                        'id' => $this->currentUser->id,
                                        'name' => $this->currentUser->full_name,
                                        'timestamp' => now()->timestamp,
                                    ]
                                ] : NULL,
                        ]
                    ))
                ->create(
                    [
                        'full_name' => $userGenInfo['full_name'],
                        'first_name' => explode(' ', $userGenInfo['full_name'])[0],
                        'last_name' => explode(' ', $userGenInfo['full_name'])[1],
                    ]
                );

            if ($key % 2 === 0) {
                $currentUsersFollowing[$searchedUser->id] =
                    [
                        'id' => $searchedUser->id,
                        'name' => $searchedUser->full_name,
                        'timestamp' => now()->timestamp,
                    ];
            }

            $searchedUsers->push($searchedUser);
        }

        Stat::factory()->for($this->currentUser)
            ->create(
                [
                    'profile_id' => $this->currentUser->profile->id,
                    'user_id' => $this->currentUser->id,
                    'following' => $currentUsersFollowing,
                    'following_count' => count($currentUsersFollowing)
                ]
            );

        $searchValue = 'en';
        $response = $this->actingAs($this->currentUser, 'api')->postJson(
            '/api/auth/searches/search',
            [

                'user_id' => $this->currentUser->id,
                'search_value' => $searchValue,
            ]
        );

        $response->assertStatus(200);

        $expectedIDs = array_values(
            array_map(
                function ($result) {
                    return $result['id'];
                },
                array_filter(
                    $usersGenInfo,
                    function ($v) use ($searchValue) {
                        if (
                            str_contains(strtolower($v['full_name']), strtolower($searchValue)) ||
                            str_contains($v['company'], $searchValue)
                        ) {
                            return $v;
                        }
                    }
                )
            )
        );

        $results = array_map(
            fn ($arr) => json_decode(json_encode($arr), true),
            $response->getData()->search_results->data
        );

        $actualFollowingIDs = array_values(
            array_map(
                function ($arr) {
                    return intval($arr['searched_user_id']);
                },
                array_filter(
                    $results,
                    function ($v) {
                        if ($v['cur_user_following']) {
                            return $v;
                        }
                    }
                )
            )
        );

        $actualIDs = array_reverse(
            array_map(
                function ($v) {
                    return intval($v['searched_user_id']);
                },
                $results
            )
        );

        $this->assertSame(
            [
                'searched_user_id',
                'full_name',
                'profile_id',
                'company',
                'profile_picture',
                'cur_user_following',
            ],
            array_keys($results[0])
        );
        $this->assertSame($expectedIDs, $actualIDs);
        $this->assertSame([4, 2], $actualFollowingIDs);
    }

    /** @test */
    public function it_saves_a_users_recent_search_result()
    {

        $searchedUser = User::factory()
            ->has(
                Profile::factory()
                    ->fullMedia()
                    ->state(
                        [
                            'id' => 2,
                            'user_id' => 2
                        ]
                    )
            )->create(
                [
                    'id' => 2
                ]
            );

        $response = $this->actingAs($this->currentUser, 'api')
            ->postJson(
                '/api/auth/searches/store',
                [
                    'user_id' => $this->currentUser->id,
                    'profile_id' => $searchedUser->profile->id,
                    'searched_user_id' => $searchedUser->id,
                    'search_value' => 'en',
                ],
            );
        $response->assertStatus(201);

        $savedSearch = Search::find(1);

        $this->assertEquals($savedSearch->searcher_user_id, $this->currentUser->id);
        $this->assertEquals($savedSearch->search_value, 'en');
        $this->assertEquals($savedSearch->searched_user_id, $searchedUser->id);
    }
    /** @test */
    public function it_does_not_save_a_duplicate_recent_search_result()
    {
        $existingUser = User::factory()
            ->has(
                Profile::factory()
                    ->fullMedia()
                    ->state(
                        [
                            'user_id' => 2,
                            'id' => 2
                        ]
                    )
            )
            ->create(
                [
                    'id' => 2
                ]
            );

        Search::factory()
            ->for(
                $this->currentUser,
                'searcherUser'
            )
            ->create(
                [
                    'searcher_user_id' => $this->currentUser->id,
                    'searched_user_id' => $existingUser->id,
                    'profile_id' => $existingUser->profile->id,
                    'search_value' => 'en',
                    'formatted_search_value' => 'En',
                ]
            );

        $response = $this->actingAs($this->currentUser, 'api')
            ->postJson(
                '/api/auth/searches/store',
                [
                    'user_id' => $this->currentUser->id,
                    'profile_id' => $existingUser->profile->id,
                    'searched_user_id' => $existingUser->id,
                    'search_value' => 'en',
                ]
            );
        $response->assertStatus(400);
        $response->assertJsonFragment(
            [
                'msg' => 'Trouble saving search result',
                'error' => 'Search has already been saved'
            ]
        );
    }

    /** @test */
    public function it_gets_the_current_users_recent_searches()
    {
        $this->assertEquals(1, 1);
        $users = User::factory()
            ->count(5)
            ->has(
                Profile::factory()
                    ->fullMedia()
            )
            ->create();

        foreach ($users as $user) {

            $searchValue = $this->faker()->text(5);

            Search::factory()
                ->for($this->currentUser, 'searcherUser')
                ->create(
                    [
                        'searcher_user_id' => $this->currentUser->id,
                        'profile_id' => $user->profile->id,
                        'searched_user_id' => $user->id,
                        'search_value' => $searchValue,
                        'formatted_search_value' => strtoupper(substr($searchValue, 0, 1)) . strtolower(substr($searchValue, 1)),
                        'created_in_unix' => now()->timestamp,
                        'purge_in_unix' => now()->timestamp + (86400 * 90),
                    ]
                );
        }

        $pages = ceil($this->currentUser->searchers->count() / 3);
        $paginatedURL = NULL;

        $searcherIDs = collect([]);
        $incrementer = 0;



        while ($incrementer < 2) {
            $url = $incrementer > 0 ? $paginatedURL :   '/api/auth/searches/' . $this->currentUser->id . '/show';

            $response = $this->actingAs($this->currentUser, 'api')
                ->getJson(
                    $url,
                    []
                );
            $incrementer++;
            $paginatedURL = $response->getData()->recent_searches->next_page_url;

            $data = $response->getData()->recent_searches->data;

            $searcherIDs->push(
                ...array_map(
                    fn ($search) => intval(
                        json_decode(json_encode($search), true)['searcher_user_id']
                    ),
                    $data
                )
            );
        }


        $actualStructure = array_map(
            fn ($search) => array_keys(json_decode(json_encode($search), true)),
            $data
        )[0];

        $expectedStructure = [
            'id',
            'profile_picture',
            'company',
            'full_name',
            'created_at',
            'updated_at',
            'searcher_user_id',
            'searched_user_id',
            'profile_id',
            'search_value',
            'formatted_search_value',
            'created_in_unix',
            'purge_in_unix',
        ];

        $response->assertStatus(200);

        $this->assertSame([1, 1, 1, 1, 1], $searcherIDs->toArray());
        $this->assertSame($expectedStructure, $actualStructure);
    }

    /** @test */
    public function it_deletes_a_current_users_search()
    {
        $users = User::factory()
            ->count(2)
            ->has(
                Profile::factory()->fullMedia()
            )
            ->create();

        foreach ($users as $user) {

            Search::factory()
                ->for($this->currentUser, 'searcherUser')
                ->create(
                    [
                        'searcher_user_id' => $this->currentUser->id,
                        'profile_id' => $user->profile->id,
                        'searched_user_id' => $user->id,
                        'search_value' => 'en',
                        'formatted_search_value' => 'En',
                        'created_in_unix' => now()->timestamp,
                        'purge_in_unix' => now()->timestamp + (86400 * 90),
                    ]
                );
        }
        $qsPayload = urlencode(json_encode(
            [
                'id' => $this->currentUser->searchers[0]->id,
                'searched_user_id' => $this->currentUser->searchers[0]->searched_user_id,
                'searcher_user_id' => $this->currentUser->id,
            ]
        ));
        $response = $this->actingAs($this->currentUser, 'api')
            ->deleteJson(
                '/api/auth/searches/' . $this->currentUser->searchers[0]->id . '/delete?ids=' . $qsPayload,
                []
            );

        $response->assertStatus(200);
        $response->assertJsonFragment(['msg' => 'Recent search(es) removed.']);

        $this->currentUser->refresh();
        $this->assertCount(1, $this->currentUser->searchers);
    }


    /** @test */
    public function it_deletes_all_of_a_current_users_search()
    {
        $users = User::factory()
            ->count(3)
            ->has(Profile::factory()
                ->fullMedia())
            ->create();

        foreach ($users as $user) {

            Search::factory()
                ->for($this->currentUser, 'searcherUser')
                ->create(
                    [
                        'searcher_user_id' => $this->currentUser->id,
                        'profile_id' => $user->profile->id,
                        'searched_user_id' => $user->id,
                        'search_value' => 'en',
                        'formatted_search_value' => 'En',
                        'created_in_unix' => now()->timestamp,
                        'purge_in_unix' => now()->timestamp + (86400 * 90),
                    ]
                );
        }

        $qsPayload = urlencode(json_encode([
            'id' => NULL,
            'searched_user_id' => NULL,
            'searcher_user_id' => $this->currentUser->id,
        ]));

        $response = $this->actingAs($this->currentUser, 'api')
            ->deleteJson(
                '/api/auth/searches/null/delete?ids=' . $qsPayload,
                []
            );

        $response->assertStatus(200);

        $this->currentUser->refresh();
        $this->assertCount(0, $this->currentUser->searchers);
    }
}
