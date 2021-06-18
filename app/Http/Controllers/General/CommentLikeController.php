<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Helpers\Comment;
use Exception;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    /*
    *Add a like to a comment
    *@param Request $request
    *@return JsonResponse
    */

    public function store(Request $request)
    {

        try {

            $comment = new Comment;

            $comment->setCommentLike($request->all());
            $commentLike = $comment->addCommentLike($request->all()['type']);

            $error = $comment->getError();

            if (gettype($error) === 'string') {
                throw new Exception($error);
            }
            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'comment_like' => $commentLike,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Bad Request',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }

    /*
    *Remove the current user's like from specified comment
    *@param Request $request
    *@param String $commentLikeId
    *@return JsonResponse
    */
    public function delete(Request $request, string $commentLikeId)
    {

        try {

            $likeData = [
                'comment_id' => $request->query('commentId'),
                'action' => $request->query('action'),
                'comment_like_id' => $commentLikeId,
                'user_id' => $request->query('userId'),
            ];
            error_log(print_r($likeData, true));
            $comment = new Comment;
            $comment->setCommentLike($likeData);

            $comment->removeCommentLike();

            $error = $comment->getError();

            if (gettype($error) === 'string') {
                throw new Exception($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success'
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()->json(
                [
                    'msg' => 'Conflict',
                    'error' => $e->getMessage()
                ],
                409
            );
        }
    }
}
