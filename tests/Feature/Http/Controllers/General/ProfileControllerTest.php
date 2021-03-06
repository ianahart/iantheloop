<?php

namespace Tests\Feature\Http\Controllers\General;

use App\Http\Controllers\General\ProfileController;
use App\Http\Requests\StoreMultipleForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Sequence;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Stat;





class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;
    use AdditionalAssertions;

    protected $backgroundFile;
    protected $profileFile;

    public function setUp(): void
    {
        parent::setUp();
        $this->backgroundFile = new UploadedFile(resource_path('assets/test_image_md.png'), 'test_image_md.png', 'image/png', null, true);
        $this->profileFile = new UploadedFile(resource_path('assets/test_image.jpg'), 'test_image.jpg', 'image/jpeg', null, true);
    }

    /** @test */
    public function it_creates_a_users_profile()
    {

        $user = User::factory()
            ->create(
                ['id' => 7, 'email' => 'beth.rippey@gmail.com']
            );

        $this->assertActionUsesFormRequest(ProfileController::class, 'store', StoreMultipleForm::class);

        $formData =
            [
                'identity' => [
                    'gender' => 'male',
                    'birth_day' => '6',
                    'birth_month' => 'Apr',
                    'birth_year' => '1950'
                ],
                'workDetails' => [
                    'company' => 'shaws',
                    'position' => 'clerk',
                    'work_city' => 'montgomery',
                    'description' => 'i work at the grocery store stocking shelves',
                    'month_from' => 'Feb',
                    'year_from' => '2013',
                    'month_to' => 'Jul',
                    'year_to' => '2021',
                ],
                'aboutDetails' => [
                    'bio' => 'this is some fill text for the about section of the paragraph here we go again and this is great. another one here we go and go.',
                    'relationship' => 'single',
                    'interests' => [
                        [
                            'name' => 'metal detecting',
                            'id' => '1'
                        ],
                        [
                            'name' => 'parachuting',
                            'id' => '2'
                        ],
                        [
                            'name' => 'space',
                            'id' => '3'
                        ],
                    ]
                ],
                'generalDetails' => [
                    'display_name' => 'dsdsdsd234',
                    'town' => 'applesed',
                    'state' => 'Connecticut',
                    'country' => 'United States',
                    'phone' => '231-341-1234',
                    'url-1' => 'www.apple.com',
                    'url-2' => 'www.bing.com',
                ],
            ];
        $formData = json_encode($formData);
        $data = [
            'data' => $formData,
            'backgroundfile' => $this->backgroundFile,
            'profilefile' => $this->profileFile,
        ];
        $token = $user->createToken('auth_token')->plainTextToken;

        Storage::fake('s3');
        /** @var mixed $user */
        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders(
                [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token
                ]
            )
            ->postJson(
                '/api/auth/profile',
                $data,
            );

        $response->assertStatus(200);
        $this->assertTrue($response->getData()->profile_created);

        $updatedUser = User::find($user->id);
        $this->assertSame($user->id, intval($updatedUser->profile->user_id));

        $this->assertTrue(str_contains($updatedUser->profile->profile_picture, $this->profileFile->getClientOriginalName()));
        $this->assertTrue(str_contains($updatedUser->profile->background_picture, $this->backgroundFile->getClientOriginalName()));
    }

    /** @test */
    public function it_updates_a_users_profile()
    {
        $user = User::factory()
            ->create(['id' => 7]);

        $profile = Profile::factory()
            ->for($user)->create(
                [
                    'links' => json_encode(
                        ['https://www.google.com', 'http://www.ebay.com']
                    ),
                    'profile_picture' =>  $this->backgroundFile,
                    'profile_filename' => $this->backgroundFile->getClientOriginalName(),
                    'display_name' => 'somethingchanged03',

                ]
            );

        $profileAttributes = $profile->getAttributes();
        $profileAttributes['interests'] = json_decode($profile->interests, true);
        $profileAttributes['links'] = json_decode($profile->links, true);

        for ($i = 0; $i < count($profileAttributes['links']); $i++) {

            $profileAttributes['url-' . strval($i)] = $profileAttributes['links'][$i];
        }

        unset($profileAttributes['links']);
        $profileAttributes['display_name'] = 'somethingchanged04';

        Storage::fake('s3');

        $strToFind = 'assets/';
        $strPos = strpos($user->profile->profile_picture, $strToFind);
        $curProPic = substr($user->profile->profile_picture, $strPos + strlen($strToFind));

        /**@var mixed $user */
        $response = $this
            ->actingAs($user, 'sanctum')
            ->patchJson(
                '/api/auth/profile/' . $profile->id . '/update',
                [
                    'data' => json_encode($profileAttributes),
                    'background_picture' => null,
                    'profile_picture' => $this->profileFile,
                ]
            );


        $response->assertStatus(200);
        $this->assertTrue($response->getData()->isUpdated);

        $user->refresh();
        $this->assertNotEquals(
            $curProPic,
            substr(
                $user->profile->profile_picture,
                strpos($user->profile->profile_picture, 'test_')
            )
        );
        $this->assertNotEquals('somethingchanged03', $user->profile->display_name);
    }

    /** @test */
    public function it_retrieves_the_users_profile_data_for_editing()
    {
        $users = User::factory()
            ->has(Profile::factory())
            ->count(3)
            ->state(new Sequence(
                ['password' => Hash::make('Password123$')],

            ))
            ->create();

        $response = $this
            ->actingAs($users[1], 'sanctum')
            ->getJson(
                '/api/auth/profile/' . strval($users[1]->profile->id) . '/edit',
                []
            );

        $response->assertStatus(200);
        $this->assertEquals($users[1]->id, $response->getData()->data->user_id);
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'user_id',
                    'background_picture',
                    'profile_picture',
                    'profile_filename',
                    'background_filename',
                    'company',
                    'interests',
                    'bio',
                    'town',
                ],
            ]
        );
    }

    /** @test */
    public function it_will_not_retrieve_the_users_profile_data_for_editing_if_not_curr_user()
    {
        $users = User::factory()
            ->count(2)
            ->has(Profile::factory())
            ->create(
                [
                    'password' => Hash::make('Password123$')
                ]
            );

        $response = $this->actingAs($users[0], 'sanctum')->getJson('/api/auth/profile/' . $users[1]->profile->id . '/edit', []);

        $response->assertStatus(403);
        $this->assertEquals('User not allowed to edit another user\'s profile', $response->getData()->error);
    }

    /** @test */
    public function it_retrieves_profile_data_for_about_section()
    {

        $user = User::factory()
            ->has(
                Profile::factory()
                    ->fullMedia()
            )
            ->create();

        /**@var mixed $user */
        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(
                '/api/auth/profile/' . $user->profile->id . '/about',
                []
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'msg',
            'profile' => array_keys($user->profile->ToArray()),
        ]);
    }

    /** @test */
    public function it_returns_not_found_if_no_profile()
    {
        $user = User::factory()
            ->has(Profile::factory())
            ->create(
                [
                    'id' => 7
                ]
            );

        /**@var mixed $user */
        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(
                '/api/auth/profile/' . strval(6) . '/about',
                []
            );
        $response->assertStatus(404);
        $this->assertEquals('Profile details were not found', $response->getData()->msg);
    }

    /** @test */
    public function it_returns_base_profile_and_profile_stats()
    {
        $subjectUser = User::factory()
            ->userProfileCreated()
            ->create(
                [
                    'id' => 7
                ]
            );

        $relatedUsers = ['following' => [], 'followers' => []];
        $notifications = [];

        foreach (User::factory()->count(5)->create() as  $index => $relatedUser) {

            foreach ($relatedUser->getAttributes() as $key => $value) {
                if (!in_array($key, ['id', 'full_name', 'created_at'])) {

                    unset($relatedUser[$key]);
                }
            }

            $relation = $index % 2 === 0 ?  'following' : 'followers';
            $notifications[$relatedUser->full_name] =

                [
                    'name' => $relatedUser->full_name,
                    'timestamp' => time(),
                    'notification' => $relation === 'following' ? 'You started following ' . $relatedUser->full_name : $relatedUser->full_name . ' has started following you.',
                ];


            $relatedUsers[$relation][$relatedUser->id] = $relatedUser;
        }

        Profile::factory()->for($subjectUser)->create(
            [
                'profile_picture' => 'https://hart-looped.s3.amazonaws.com/60a6c999541f0weatherman_pro_2.jpeg',
                'profile_filename' => '60a6c999541f0weatherman_pro_2.jpeg',
                'background_picture' => 'https://hart-looped.s3.amazonaws.com/60a6c87f300ceweatherman_bg-2.jpeg',
                'background_filename' => '60a6c87f300ceweatherman_bg-2.jpeg',
            ]
        );
        Stat::factory()
            ->for($subjectUser)
            ->create(
                [
                    'name' => $subjectUser->full_name,
                    'profile_id' => $subjectUser->profile->id,
                    'following' => json_encode($relatedUsers['following']),
                    'followers' => json_encode($relatedUsers['followers']),
                    'notifications' => json_encode($notifications),
                    'following_count' => count($relatedUsers['following']),
                    'followers_count' => count($relatedUsers['followers']),
                ]
            );

        $response = $this
            ->actingAs($subjectUser, 'sanctum')
            ->getJson(
                '/api/auth/profile/' . $subjectUser->id,
                []
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'msg',
            'profile',
            'currUserFollowing',
            'currUserHasRequested',
        ]);
        $this->assertSame(json_encode($relatedUsers['following']), $response->getData()->stats->following);
        $this->assertSame(json_encode($relatedUsers['followers']), $response->getData()->stats->followers);
    }
}
