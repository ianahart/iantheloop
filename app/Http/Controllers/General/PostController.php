<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Models\User;
use App\Models\Post;
use App\Helpers\Posts;

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

                $this->addPost($this->user, $this->post, $formData);

                return response()
                    ->json(
                        [
                            'msg' => 'request successful',
                            'data' => 'hi'
                        ],
                        201
                    );
            }
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'request failed',
                        'error' => $e->getMessage(),
                        'intercept' => false,
                    ],
                    401
                );
        }
    }

    /*
    * helper func for @store
    * @param object $user
    * @param object $post
    * @param array $data
    * @return void
    */
    public function addPost(object $user, object $post, array $data)
    {
        $post = new Posts($user, $post);

        $post->setData($data);
        $post->addPost();
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

            $post->findPosts();

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
}
