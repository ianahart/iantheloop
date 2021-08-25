<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stat;
use App\Models\FollowSuggestion as FollowSuggestionModel;
use App\Helpers\FollowRequest;
use App\Helpers\Statistics;


use Exception;



class StatsController extends Controller
{

    /*
     * the user being followed is added to the current user's following column
     * the current user is added to the user being followed follower's column
     * @param string $userId
     * @param Request $request
     * @return JsonResponse
     */
    public function updateFollowStats(Request $request, string $userId)
    {
        try {

            $viewingUserId = $request->all()['viewingUserId'];
            $requestId = $request->all()['requestId'];

            $followRequest = new FollowRequest;

            $followRequest->setReceiverUserId($viewingUserId);
            $followRequest->removeFollowRequest(intval($requestId));


            $currentUserStat = Stat::where('user_id', '=', $userId)->first();
            $viewingUserStat = Stat::where('user_id', '=', $viewingUserId)->first();

            $statisticSubject = new Statistics($viewingUserStat, $currentUserStat);
            $statisticUser = new Statistics($currentUserStat, $viewingUserStat);

            $statisticUser->setUserList($currentUserStat->following);
            $statisticUser->setUserId($viewingUserStat->user_id);
            $statisticUser->setListCount($currentUserStat->following_count);
            $statisticUser->setNotifications($currentUserStat->notifications);
            $statisticUser->setStatus('following');

            $statisticSubject->setUserList($viewingUserStat->followers);
            $statisticSubject->setUserId($currentUserStat->user_id);
            $statisticSubject->setListCount($viewingUserStat->followers_count);
            $statisticSubject->setNotifications($viewingUserStat->notifications);
            $statisticSubject->setStatus('followed');

            $statisticUser->addFollow();
            $statisticSubject->addFollow();


            $currentUserStat->following = $statisticUser->getUserList();
            $currentUserStat->following_count = $statisticUser->getListCount();
            $viewingUserStat->followers = $statisticSubject->getUserList();
            $viewingUserStat->followers_count = $statisticSubject->getListCount();

            $statisticUser->addNotification();
            $statisticSubject->addNotification();

            $currentUserStat->notifications = $statisticUser->getNotifications();
            $viewingUserStat->notifications = $statisticSubject->getNotifications();

            $followSuggestion = FollowSuggestionModel::where('unique_identifier', '=', $viewingUserId . '_' . $userId)->first();
            if ($followSuggestion) {
                $followSuggestion->suggest = 0;
                $followSuggestion->pending = 0;
                $followSuggestion->save();
            }

            $viewingUserStat->save();
            $currentUserStat->save();


            return response()
                ->json(
                    [
                        'msg' => 'user followed',
                        'stats' => $viewingUserStat,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => $e->getMessage()
                    ],
                    400
                );
        }
    }

    /*
    * unfollow the viewing user
    * @param string $userId
    * @param Request $request
    * @return JsonResponse
    */

    public function updateUnfollowStats(Request $request, string $userId)
    {

        try {


            $viewingUserId = $request->all()['viewingUserId'];

            $currentUserStat = Stat::where('user_id', '=', $userId)->first();
            $viewingUserStat = Stat::where('user_id', '=', $viewingUserId)->first();

            $statisticSubject = new Statistics($viewingUserStat, $currentUserStat,);
            $statisticUser = new Statistics($currentUserStat, $viewingUserStat);

            $statisticUser->setUserList($currentUserStat->following);
            $statisticUser->setUserId($viewingUserStat->user_id);
            $statisticUser->setListCount($currentUserStat->following_count);
            $statisticUser->setNotifications($currentUserStat->notifications);
            $statisticUser->setStatus('unfollowed');

            $statisticSubject->setUserList($viewingUserStat->followers);
            $statisticSubject->setUserId($currentUserStat->user_id);
            $statisticSubject->setListCount($viewingUserStat->followers_count);
            $statisticSubject->setNotifications($viewingUserStat->notifications);
            $statisticSubject->setStatus('unfollowed by');

            $statisticUser->removeFollow();
            $statisticSubject->removeFollow();

            $currentUserStat->following = $statisticUser->getUserList();
            $currentUserStat->following_count = $statisticUser->getListCount();
            $viewingUserStat->followers = $statisticSubject->getUserList();
            $viewingUserStat->followers_count = $statisticSubject->getListCount();

            $statisticUser->addNotification();
            $statisticSubject->addNotification();

            $currentUserStat->notifications = $statisticUser->getNotifications();
            $viewingUserStat->notifications = $statisticSubject->getNotifications();

            $currentUserStat->save();
            $viewingUserStat->save();

            return response()
                ->json(
                    [
                        'msg' => 'user unfollowed',
                        'stats' => $viewingUserStat,
                        'currUserFollowing' => false
                    ]
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    ['msg' => $e->getMessage()],
                    400
                );
        }
    }
}
