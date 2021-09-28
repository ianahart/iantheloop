<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreSearchRequest;
use App\Helpers\Search;
use Exception;

class SearchController extends Controller
{
    /**
     * Store a user's search result
     * @param StoreSearchRequest $request
     * @return JsonResponse
     */
    public function search(StoreSearchRequest $request)
    {
        $request->validated();

        [$userId, $searchValue] = array_values($request->all());
        $search = new Search($userId);

        $search->setSearchValue($searchValue);
        $yieldsResults = $search->processSearch();

        $exception = $search->getException();

        if (count($exception)) {
            throw new Exception($exception['error']);
        }

        $searchResults = $yieldsResults ? $search->getResults() : [];

        try {
            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'search_results' => $searchResults,
                    ],
                    200
                );
        } catch (Exception $e) {
            $code = !is_null($exception['code']) ? intval($exception['code']) : 400;
            return response()
                ->json(
                    [
                        'msg' => 'Unable to process search request',
                        'error' => $e->getMessage()
                    ],
                    $code
                );
        }
    }

    /**
     * Add a user that was clicked on to searches
     * @param Request $request
     */
    public function store(Request $request)
    {
        try {

            $search = new Search($request->all()['user_id']);

            $search->saveSearch($request->all());

            $exception = $search->getException();

            if (count($exception)) {
                throw new Exception($exception['error']);
            }

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'data' => 'data here'
                    ],
                    201
                );
        } catch (Exception $e) {
            $code = !is_null($exception['code']) ? intval($exception['code']) : 400;

            return response()
                ->json(
                    [
                        'msg' => 'Trouble saving search result',
                        'error' => $e->getMessage()
                    ],
                    $code
                );
        }
    }

    /**
     * gets the current user's recent searches
     * @param Request $request
     * @param String $userId
     * @return JsonResponse
     */
    public function show(Request $request, String $userId)
    {
        try {
            $search = new Search(intval($userId));

            $search->recent();

            $exception = $search->getException();

            if (count($exception)) {
                throw new Exception($exception['error']);
            }

            $recentSearches = $search->getResults();

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'recent_searches' => $recentSearches,
                    ],
                    200
                );
        } catch (Exception $e) {
            $code = !is_null($exception['code']) || !strlen($exception['code']) ? intval($exception['code']) : 404;

            return response()
                ->json(
                    [
                        'msg' => 'Unable to get most recent searches.',
                        'error' => $e->getMessage()
                    ],
                    $code
                );
        }
    }

    /**
     * Delete a recent search or all recent searches
     * @param Request $request
     * @param String $searchId
     * @return JsonResponse
     */
    public function delete(Request $request, String $searchId)
    {
        try {

            $data = json_decode($request->all()['ids'], true);

            $search = new Search(intval($data['searcher_user_id']));

            $search->removeSearch($data);

            $exception = $search->getException();

            if (count($exception)) {
                throw new Exception($exception['error']);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Recent search(es) removed.'
                    ],
                    200
                );
        } catch (Exception $e) {
            $code = !is_null($exception['code']) || isset($exception['code']) ?
                intval($exception['code']) : 500;

            return response()
                ->json(
                    [
                        'msg' => 'Unable to remove recent search(es).',
                        'error' => $e->getMessage()
                    ],
                    $code
                );
        }
    }
}
