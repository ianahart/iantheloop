<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCommentRequest;
use App\Helpers\Comment;

class CommentController extends Controller
{

    /*
    *Store a users comment for the specified post
    *@param StoreCommentRequest $request
    *@return JsonResponse
    */
    public function store(StoreCommentRequest $request)
    {
        try {

            $validated = $request->validated();

            if (!$validated) {
                throw new Exception();
            }

            $comment = new Comment;

            $comment->setPostId($request->all()['post_id']);
            $comment->setUserId($request->all()['user_id']);
            $comment->setCommentText($request->all()['input']);

            $latestComment = $comment->addComment();

            $error = $comment->getError();

            if (gettype($error) === 'string') {

                throw new Exception($error, $comment->getCode());
            }

            return response()
                ->json(
                    [
                        'msg' => 'Comment added',
                        'latest_comment' => $latestComment,
                    ],
                    201
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Failed to add comment',
                        'error' => [$e->getMessage()],
                        'code' => $e->getCode()
                    ],
                    $e->getCode(),
                );
        }
    }
}
