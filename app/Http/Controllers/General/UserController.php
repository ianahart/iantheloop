<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Status;
use Exception;
use App\Models\User;
use App\Models\Review;


class UserController extends Controller
{
    /*
  * update the users status to either "online" or "offline"
  * @param Request $request
  * @param string $userId
  * @return void
  */
    public function updateUserStatus(Request $request, string $userId)
    {
        try {


            if (Auth::user()->id !== intval($userId)) {

                throw new Exception('User is forbidden from making this request');
            }

            $userStatus = new Status($userId);

            $userStatus->updateStatus(true, $request->all()['status']);

            $exception = $userStatus->getException();

            if ($exception) {

                throw new Exception($exception);
            }

            $newUserStatus = $userStatus->getUserStatus();

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'new_user_status' => $newUserStatus,
                        'status_updated' => true,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'conflict',
                        'error' => $e->getMessage(),
                        'intercept' => false,
                        'status_updated' => false,
                    ],
                    403
                );
        }
    }

    /*
  * update a given column on the user"
  * @param Request $request
  * @param string $userId
  * @return void
  */
    public function updateColumn(Request $request, $userId)
    {
        try {

            $curUser = User::find($userId);
            $column = $request->all()['column'];
            $curUser->$column = $request->all()['value'];

            $curUser->save();

            return response()
                ->json(
                    [
                        'msg' => 'success'
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Unable to update',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }
    /**
     * Get the total count of users
     * @param Request $request
     * @return JsonResponse
     */
    public function count(Request $request)
    {

        try {
            $count = User::count();
            $reviewCount = Review::count();

            if (is_null($count) || !$count) {
                $count = 0;
            }

            if (is_null($reviewCount) || !$reviewCount) {
                $reviewCount = 0;
            } else {
                $avgReviewRating = Review::avg('rating');
                $avgReviewRating = round($avgReviewRating, 1);
            }

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'count' => $count,
                        'review_count' => $reviewCount,
                        'avg_review_rating' => $avgReviewRating,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to get user count',
                        'error' => $e->getMessage()
                    ],
                    500
                );
        }
    }
}
