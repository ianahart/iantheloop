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

            if (array_key_exists('exception', $userList)) {

                throw new Exception($userList['exception']);
            }


            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'profiles' => $userList['profiles'],
                        'last_collection_item' => $userList['last_collection_item'],
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

            if (array_key_exists('exception', $userList)) {

                throw new Exception($userList['exception']);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'profiles' => $userList['profiles'],
                        'last_collection_item' => $userList['last_collection_item'],
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

        $userList = [];

        $userNetwork = new Network($this->stat, $this->profile, $this->user);

        $userNetwork->setUserId($userId);

        $userNetwork->setQueryFields($queryFields);
        $userNetwork->setCurIndex($index);
        $userNetwork->setPage($page);
        $userNetwork->setUserProfiles([]);

        $userExists = $userNetwork->checkUserExists();

        if (!$userExists) {

            return  ['exception' => 'User not found'];
        }

        $userNetwork->queryOwnerProfilePic();
        $ownerProfilePic = $userNetwork->getOwnerProfilePic();

        $userNetwork->pluckFollowingMetaData();
        $exception = $userNetwork->getException();

        if (isset($exception)) {

            return ['exception' => $exception];
        }

        $lastCollectionItem = $userNetwork->getLastCollectionItem();


        if ($lastCollectionItem !== $index) {

            $userNetwork->makeUserList();
            $listCount = $userNetwork->getListCount();
        }

        $exception = $userNetwork->getException();

        if (isset($exception)) {

            return ['exception' => $exception];
        }

        $profiles = $userNetwork->getProfiles();

        return
            [
                'profiles' => $profiles,
                'last_collection_item' => $lastCollectionItem,
                'list_count' => $listCount,
                'owner_profile_pic' => $ownerProfilePic,
                'owner_name' => $userExists->full_name,
            ];
    }
}
