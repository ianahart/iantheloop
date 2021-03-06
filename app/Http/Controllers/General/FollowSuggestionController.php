<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\FollowSuggestion;
use Exception;

class FollowSuggestionController extends Controller
{
    /*
     * Paginate Follow Suggestions for the current user
     * @param string $userId
     * @param Request $request
     * @return JsonResponse
     */

    public function show(Request $request, string $userId)
    {
        try {
            $lastSuggestion = intval($request->query('last'));
            $endOfSuggestions = $request->query('end') === '0' ? false : true;

            $followSuggestion = new FollowSuggestion(intval($userId));

            if ($endOfSuggestions) {

                $followSuggestion->aggregate();
                $followSuggestion->retrieve($lastSuggestion);
            } else {

                $followSuggestion->checkSuggestionsExist();

                if (!$followSuggestion->getSuggestionsExist()) {
                    $followSuggestion->aggregate();
                }
                $followSuggestion->retrieve($lastSuggestion);
            }

            $exception = $followSuggestion->getError();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }

            $followSuggestions = $followSuggestion->getFollowSuggestions();
            $total = $followSuggestion->getTotal();


            return response()
                ->json(
                    [
                        'msg' => 'Follow suggestions retrieved',
                        'follow_suggestions' => $followSuggestions,
                        'total' => $total,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to load follow suggestions right now',
                        'error' => $e->getMessage()
                    ]
                );
        }
    }

    /*
     * Update suggestion to "rejected" so user will not see suggestion for x amount of time
     * Or update suggestion to "not rejected" so user will see the suggestion again
     * @param string $suggestionId
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request, string $suggestionId)
    {

        try {
            error_log(print_r('Suggestion Id: ' . $suggestionId, true));
            error_log(print_r($request->all(), true));

            $followSuggestion = new FollowSuggestion(intval($request->all()['current_user_id']));
            $followSuggestion->updateFollowSuggestion($request->all(), $suggestionId);

            $exception = $followSuggestion->getError();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Follow suggestion updated'
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to preform update on follow suggestion',
                        'error' => $e->getMessage(),
                    ],
                    500
                );
        }
    }
}
