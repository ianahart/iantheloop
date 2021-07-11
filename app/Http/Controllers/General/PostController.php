<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Models\User;
use App\Models\Post;
use App\Helpers\Posts;
use Tymon\JWTAuth\Facades\JWTAuth;


class PostController extends Controller
{

    public object $user;
    public object $post;

    public function __construct(User $user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    /*
    * Store a post supplied by user
    * @param StoragePostRequest $request
    * @return JsonResponse
    */

    public function store(StorePostRequest $request)
    {
        try {

            $validated = $request->validated();

            if ($validated) {


                $formData = $request->all();

                foreach ($formData as $key => $val) {

                    unset($formData['data']);
                }

                $newPost = $this->addPost($this->user, $this->post, $formData);

                return response()
                    ->json(
                        [
                            'msg' => 'request successful',
                            'new_post' => $newPost
                        ],
                        201
                    );
            }
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'request failed',
                        'errors' => $e->getMessage(),
                        'intercept' => false,
                    ],
                    403
                );
        }
    }

    /*
    * helper func for @store
    * @param object $user
    * @param object $post
    * @param array $data
    * @return object;
    */
    public function addPost(object $user, object $post, array $data)
    {
        $post = new Posts($user, $post);

        $post->setData($data);

        $post->setSubjectUserId(intval($data['subject_user_id']));
        $post->addPost();
        $newPost = $post->getNewPost();

        return array_shift($newPost);
    }

    /*
    * Get posts that belong to the subject being viewed
    * @param  Request $request
    * @return JsonResponse
    */
    public function index(Request $request)
    {
        try {

            $moreRecords = true;

            $subjectUserId = $request->query('subjectId');
            $lastPostItem = $request->query('lastPost');


            $post = new Posts($this->user, $this->post);

            $post->setSubjectUserId(intval($subjectUserId));
            $post->setLastPostItem(intval($lastPostItem));
            $post->setCurrentUserId(JWTAuth::user()->id);

            $post->findPosts($request->query('filters'));

            if ($post->getException() === 'all records fetched') {

                $moreRecords = false;
            }

            if ($moreRecords) {

                $posts = $post->getPosts();

                $lastPostItem = $post->getLastPostItem();
            } else {

                $posts = null;
                $lastPostItem = null;
            }

            return response()
                ->json(
                    [
                        'msg' => 'request success',
                        'posts' => $posts,
                        'last_post_item' => $lastPostItem,
                        'more_records' => $moreRecords,
                    ],
                    200
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'request failed',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /*
    * Delete the specified post
    * @param  Request $request
    * @param string $id
    * @return JsonResponse
    */
    public function delete(Request $request, string $id)
    {
        try {

            $post = new Posts($this->user, $this->post);

            $post->setCurrentUserId(intval($request->query('user')));
            $post->setActivePostId(intval($id));

            $post->deletePost();

            $exception = $post->getException();
            $statusCode  = null;

            if ($exception) {

                $statusCode = $post->getStatusCode();

                return response()
                    ->json(
                        [
                            'msg' => 'Sorry, something went wrong',
                            'error' => $exception
                        ],
                        $statusCode
                    );
            }

            return response()
                ->json(
                    [
                        'msg' => 'post deleted'
                    ],
                    200
                );
        } catch (Exception $e) {


            return response()
                ->json(
                    [
                        'msg' => 'Unable to remove post',
                        'error' => $e->getMessage(),
                    ],
                    404
                );
        }
    }
}
