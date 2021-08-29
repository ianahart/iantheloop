<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReviewRequest;
use App\Helpers\Review;
use App\Helpers\Status;
use Exception;

class ReviewController extends Controller
{
    /**
     * Store a review for the current user
     * @param StoreReviewRequest $request
     * @return JSONResponse
     */
    public function store(StoreReviewRequest $request)
    {
        try {

            $validated = $request->validated();

            if ($validated) {

                $review = new Review;
                $review->setUserId(intval($request->all()['currentUserId']));
                $review->setReview([
                    'text' => $request->all()['review'],
                    'rating' => intval($request->all()['rating'])
                ]);

                $review->add();

                $error = $review->getError();

                if (!is_null($error)) {
                    throw new Exception($error);
                }
            }

            return response()
                ->json(
                    [
                        'msg' => 'Review submitted'
                    ],
                    201
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'Review not submitted',
                        'error' => $e->getMessage(),
                    ],
                    $e->getCode() ? $e->getCode() : 400
                );
        }
    }

    /**
     * Get all the reviews to be displayed
     * @param Request $request
     * @return JSONResponse
     */
    public function index(Request $request)
    {
        try {

            $user = new Status(null);

            $authenticationStatus = false;

            if (null !== $request->header('authorization')) {
                $token = explode(' ', $request->header('authorization'));
                if ($token) {
                    $authenticationStatus = $user->checkToken($token[1]);
                }
            }

            $review = new Review;

            $hasReviewed = false;
            if ($authenticationStatus) {
                $hasReviewed = $review->reviewStatus();
            }

            $review->retrieve($request->query('page'));

            $error = $review->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            $total = $review->getTotal();
            $page = $review->getPage();
            $reviews = $review->getReviews();

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'authenticated' => $authenticationStatus,
                        'submit_status' => $hasReviewed,
                        'page' => $page,
                        'total' => $total,
                        'reviews' => $reviews,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to retrieve reviews',
                        'error' => $e->getMessage()
                    ],
                    $e->getCode() ? $e->getCode() : 404
                );
        }
    }
}
