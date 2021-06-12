<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Helpers\PostLikes;
use App\Models\PostLike;
use App\Models\Post;

class PostLikeController extends Controller
{

    public object $postLike;
    public object $post;

    public function __construct(PostLike $postLike, Post $post)
    {
        $this->postLike = $postLike;
        $this->post = $post;
    }

    /*
    * Store a like belonging to a certain post
    * @param Request $request
    * @return JsonResponse
    */
    public function store(Request $request)
    {

        try {

            $postLikes = new PostLikes($this->postLike, $this->post);

            $postLikes->setPostId(intval($request->all()['post_id']));
            $postLikes->setCurrentUserId(intval($request->all()['current_user_id']));

            $postLikes->addLikeToPost();
            $newLike = $postLikes->getNewLike();


            return response()
                ->json(
                    [
                        'msg' => 'Post has been liked',
                        'new_like' => $newLike,
                    ],
                    201
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'mgs' => 'something went wrong',
                        'error' => $e->getMessage()
                    ],
                    409
                );
        }
    }

    /*
    * Delete a like on the post by the current user
    * @param Request $request
    * @return JsonResponse
    */
    public function delete(Request $request)
    {

        try {

            $postLikes = new PostLikes($this->postLike, $this->post);

            $postLikes->setPostLikeToDel($request->all());



            $postLikes->deletePostLike();

            $exception = $postLikes->getException();

            if (isset($exception)) {

                throw new Exception($exception);
            }

            return response()
                ->json(
                    [
                        'msg' => 'like removed from post'
                    ],
                    204
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'failed to remove like',
                        'error' => $e->getMessage()
                    ],
                    409
                );
        }
    }
}
