<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoryPostRequest;
use App\Helpers\Story;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoryController extends Controller
{

    /**
     * Returns the count of stories posted by a specified user in the past day
     * @param Request $request
     * @param String $userId
     * @return JsonResponse
     */
    public function count(Request $request, String $userId)
    {
        try {

            $story = new Story(JWTAuth::user()->id);

            $userStoriesCount = $story->userHasStories(intval($userId));

            if (!is_null($story->getError())) {
                throw new Exception($story->getError());
            }

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'user_stories_count' => $userStoriesCount
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to get the number of stories posted by the specified user: ' . $userId,
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /**
     * It creates a story that the current user is submitting
     * @param StoryPostRequest $request
     * @return JsonResponse
     */
    public function store(StoryPostRequest $request)
    {
        try {

            $request->validated();

            $newStory = $request->all();
            unset($newStory['data']);

            $story = new Story(intval($newStory['user_id']));

            $maxStories = $story->storyLimit();

            if ($maxStories) {
                throw new Exception('You\'ve posted the maximum number of stories for today (5)');
            }

            $story->add($newStory);

            $error = $story->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'data' => []
                    ],
                    201
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to create story',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }

    /**
     * It returns stories that the specified user has uploaded
     * @param  Request $request
     * @param String $userId
     * @return JsonResponse
     */
    public function show(Request $request, String $userId)
    {
        try {

            $story = new Story(JWTAuth::user()->id);

            $story->removeExpiredUserStories(intval($userId));

            if (!is_null($story->getError())) {
                throw new Exception($story->getError());
            }

            $story->specifiedUserStory(intval($userId));

            $error = $story->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            $stories = $story->getStories();

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'stories' => $stories,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to retrieve the user\'s stories',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }
}
