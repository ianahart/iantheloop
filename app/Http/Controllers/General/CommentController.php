<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCommentRequest;
use App\Helpers\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /*
    *Delete a comment if owner of the comment
    *@param Request $request
    *@param String  $commentID
    *@return JsonResponse
    */
    public function delete(Request $request, string $commentID)
    {
        try {

            $error = $this->initCommentProcess($request, $commentID);

            if (gettype($error) === 'string') {
                throw new Exception($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'successfully deleted',
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Forbidden action',
                        'error' => $e->getMessage(),
                        'intercept' => false
                    ],
                    403
                );
        }
    }

    /*
    *Retrieve the specified post's comments
    *@param Request $request
    *@param String  $postID
    *@return JsonResponse
    */
    public function show(Request $request, string $postID)
    {
        try {

            $comment = new Comment;

            $comment->setPostId(intval($postID));
            $comment->setLastComment(intval($request->query('last')));

            $comments = $comment->refillComments('comment');

            $error = $comment->getError();

            if (gettype($error) === 'string') {
                throw new ModelNotFoundException($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'post_comments' => $comments,
                    ],
                    200
                );
        } catch (ModelNotFoundException $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Comments not found',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /*
    *store a reply to a comment
    *@param StoreCommentRequest $request
    *@param void
    *@return JsonResponse
    */

    public function replyStore(StoreCommentRequest $request)
    {

        try {

            $validated = $request->validated();

            if (!$validated) {
                throw new Exception();
            }

            $comment = new Comment;

            $comment->setPostId($request->all()['post_id']);
            $comment->setUserId($request->all()['user_id']);
            $comment->setCommentId($request->all()['reply_to_comment_id']);
            $comment->setCommentText($request->all()['input']);

            $latestComment = $comment->addComment();

            $error = $comment->getError();

            if (gettype($error) === 'string') {

                throw new Exception($error, $comment->getCode());
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'reply_comment' => $latestComment,
                    ],
                    201
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Something went wrong, Bad Request',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }

    /*
    *retrieve more reply comments that are older than the one specified
    *@param $request
    *@param String $postId
    *@return JsonResponse
    */

    public function showReply(Request $request, String $postId)
    {

        try {

            $commentRepliedTo = intval($request->query('replyTo'));

            $comment = new Comment;

            $comment->setPostId(intval($postId));
            $comment->setLastComment(intval($request->query('last')));
            $comment->setCommentId($commentRepliedTo);

            $replyComments = $comment->refillComments('reply');

            $error = $comment->getError();

            if (gettype($error) === 'string') {
                throw new ModelNotFoundException($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'replyComments' => $replyComments,
                        'postId' => intval($postId),
                        'commentRepliedTo' => $commentRepliedTo,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Comments not found or Comments all loaded',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /*
    *Delete a reply comment if owner of the reply comment or if it is posted on the user's wall
    *@param Request $request
    *@param String  $commentID
    *@return JsonResponse
    */
    public function deleteReply(Request $request, String $commentID)
    {
        try {

            $error = $this->initCommentProcess($request, $commentID);

            if (gettype($error) === 'string') {
                throw new Exception($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'reply successfully deleted',
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Forbidden action',
                        'error' => $e->getMessage(),
                        'intercept' => false
                    ],
                    403
                );
        }
    }

    /*
    * Cleans up duplicate code needed in both @delete and @deleteReply
    *@param Request $request
    *@param String $commentID
    *@return String|NULL
    */
    private function initCommentProcess(Request $request, String $commentID)
    {

        $comment = new Comment;

        $comment->setUserId(intval($request->query('uid')));
        $comment->setCommentId(intval($commentID));

        $comment->deleteComment($request->query('type'));

        $error = $comment->getError();

        return $error;
    }
}
