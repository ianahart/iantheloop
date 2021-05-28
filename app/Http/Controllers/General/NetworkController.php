<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Helpers\Network;
use App\Models\Stat;
use App\Models\Profile;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class NetworkController extends Controller
{

    public object $stat;
    public object $profile;
    public object $user;


    public function __construct(Stat $stat, Profile $profile, User $user)
    {
        $this->stat = $stat;
        $this->profile = $profile;
        $this->user = $user;
    }



    /*
     * Get the user's following list
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function showFollowing(Request $request, string $userId)
    {

        try {

            $queryFields = ['following', 'following_count'];
            $page = $request->query('page');
            $index = $request->query('index');
            $userList = $this->getUserList($queryFields, $userId, $page, $index);

            if (array_key_exists('error', $userList)) {

                throw new Exception('User not found.');
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'profiles' => $userList['profiles'],
                        'list_count' => $userList['list_count'],
                        'owner_profile_pic' => $userList['owner_profile_pic'],
                        'owner_name' => $userList['owner_name'],
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'error',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /*
     * Get the user's following list
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function showFollowers(Request $request, string $userId)
    {

        try {

            $queryFields = ['followers', 'followers_count'];
            $page = $request->query('page');
            $index = $request->query('index');
            $userList = $this->getUserList($queryFields, $userId, $page, $index);

            if (array_key_exists('error', $userList)) {

                throw new Exception('User not found.');
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'profiles' => $userList['profiles'],
                        'list_count' => $userList['list_count'],
                        'owner_profile_pic' => $userList['owner_profile_pic'],
                        'owner_name' => $userList['owner_name'],
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'error',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }
    /*
     * collect data for following and followers
     * @param Request $request
     * @param array $queryFields
     *  @param string $userId
     *  @param string $page
     *  @param string $index
     * @return array;
     */

    private function getUserList($queryFields, $userId, $page, $index)
    {
        try {

            $userNetwork = new Network($this->stat, $this->profile, $this->user);

            $userNetwork->setUserId($userId);

            $userNetwork->setQueryFields($queryFields);
            $userNetwork->setCurIndex($index);
            $userNetwork->setPage($page);
            $userNetwork->setUserProfiles([]);

            $userExists = $userNetwork->checkUserExists();

            if (!$userExists) {

                throw new Exception('User not Found');
            }

            $userNetwork->queryOwnerProfilePic();
            $ownerProfilePic = $userNetwork->getOwnerProfilePic();

            $userNetwork->pluckFollowingMetaData();

            $listCount = $userNetwork->getListCount();


            if ($listCount > 0) {

                $userNetwork->makeUserList();
            }

            $exception = $userNetwork->getException();

            if (isset($exception)) {

                throw new Exception($exception);
            }

            $profiles = $userNetwork->getProfiles();

            return [
                'profiles' => $profiles,
                'list_count' => $listCount,
                'owner_profile_pic' => $ownerProfilePic,
                'owner_name' => $userExists->full_name
            ];
        } catch (Exception $e) {

            return ['error' => $e->getMessage()];
        }
    }
}
