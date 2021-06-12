<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Helpers\FlaggedPosts;
use Mockery\CountValidator\Exact;

class FlaggedPostController extends Controller
{

    /*
      add a post to the flagged_posts table
      @param Request $request
      @return JsonResponse
    */
    public function store(Request $request)
    {

        try {

            $flaggedPost = new FlaggedPosts;

            $flaggedPost->setUserId($request->all()['user_id']);
            $flaggedPost->setPostId($request->all()['post_id']);
            $flaggedPost->setFlaggedData($request->all()['reasons']);

            $flaggedPost->addFlaggedPost();

            if (!is_null($flaggedPost->getError())) {

                throw new Exception($flaggedPost->getError());
            }

            return response()
                ->json(
                    [
                        'msg' => 'flagged post successfully stored',
                        'data' => 'data goes here'
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Unable to store flagged post',
                        'error' => $e->getMessage()
                    ],
                    409
                );
        }
    }
}
