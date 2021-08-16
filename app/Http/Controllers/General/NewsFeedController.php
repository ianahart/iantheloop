<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helpers\Posts;
use App\Models\User;
use App\Models\Post;

use Exception;

class NewsFeedController extends Controller
{

    public object $user;
    public object $post;

    public function __construct(User $user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    /*
    * Get all posts from users authored by them that the logged in user is following
    * @param Request $request
    * @param string $slug
    * @return JsonResponse
    */
    public function show(Request $request, string $slug)
    {
        try {
            if (intval($request->query('subjectId')) !== intval(JWTAuth::user()->id)) {

                return response()->json(['msg' => 'forbidden'], 403);
            }
            $moreRecords = true;

            $lastPostItem = $request->query('lastPost');
            $subjectUserId = $request->query('subjectId');

            $posts = new Posts($this->user, $this->post);

            $posts->setLastPostItem(intval($lastPostItem));
            $posts->setSubjectUserId(intval($subjectUserId));

            $posts->newsfeedPosts($slug);

            $exception = $posts->getException();


            if (!is_null($exception)) {
                if (strtolower($exception) === 'all records fetched') {
                    $moreRecords = false;
                } else {
                    throw new Exception($exception);
                }
            }
            if (!$moreRecords) {
                $newsfeedPosts = null;
                $lastPostItem = null;
            } else {
                $newsfeedPosts = $posts->getPosts();
                $lastPostItem = $posts->getLastPostItem();
            }

            return response()->json(
                [
                    'msg' => 'request success',
                    'posts' => $newsfeedPosts,
                    'last_post_item' => $lastPostItem,
                    'more_records' => $moreRecords,
                ],
                200
            );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to load posts for your newsfeed',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }
}
