<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
}
