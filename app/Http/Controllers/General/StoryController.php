<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoryPostRequest;
use App\Helpers\Story;
use Exception;

class StoryController extends Controller
{
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
}
