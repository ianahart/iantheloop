<?php

namespace App\Http\Controllers\general;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Statistics;
use App\Models\Stat;
use Exception;



class StatsController extends Controller
{

    /*
     * the user being followed is added to the current user's following column
     * the current user is added to the user being followed follower's column
     * @param string $userId
     * @param Request $request
     * @return JsonResponse (to be determined)
     */
    public function updateFollowStats(Request $request, string $userId)
    {
        try {

            $viewingUserId = $request->all()['viewingUserId'];


            $currentUserStat = Stat::where('user_id', '=', $userId)->first();
            $viewingUserStat = Stat::where('user_id', '=', $viewingUserId)->first();

            $statistic = new Statistics;

            $statistic->viewingUser = (object) $viewingUserStat->getAttributes();
            $statistic->currentUser = (object) $currentUserStat->getAttributes();
            $statistic->timestamp = time();

            $statistic->updateFollow();

            $viewingUserStat->followers_count = $statistic->viewingUser->followers_count;
            $currentUserStat->following_count = $statistic->currentUser->following_count;

            $viewingUserStat->followers = $statistic->viewingUser->followers;
            $currentUserStat->following = $statistic->currentUser->following;

            $viewingUserStat->notifications = $statistic->viewingUser->notifications;
            $currentUserStat->notifications = $statistic->currentUser->notifications;

            $currentUserStat->save();
            $viewingUserStat->save();

            return response()
                ->json(
                    [
                        'msg' => 'Resource updated.',
                        'stats' => $statistic->viewingUser,
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
}
