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

            $review->retrieve(intval($request->query('page')), $request->query('filters'));

            $error = $review->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            $pagination = $review->getPagination();
            $reviews = $review->getReviews();

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'authenticated' => $authenticationStatus,
                        'submit_status' => $hasReviewed,
                        'pagination' => $pagination,
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

    /**
     * Get all the reviews to be displayed
     * @param Request $request
     * @param String $userId
     * @return JSONResponse
     */
    public function show(Request $request, String $userId)
    {
        try {

            $review = new Review;
            $review->setUserId(intval($userId));

            $review->userReview();
            $exception = $review->getError();

            if (!is_null($exception)) {
                throw new Exception($exception, 404);
            }

            $review = $review->getReviews();


            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'review' => [$review],
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to retrieve review',
                        'error' => $e->getMessage()
                    ],
                    $e->getCode() ? $e->getCode() : 404
                );
        }
    }

    /**
     * Update current user's review
     * @param StoreReviewRequest $request
     * @param String $reviewId
     * @return JSONResponse
     */
    public function update(StoreReviewRequest $request, String $reviewId)
    {
        try {
            $request->validated();

            $review = new Review;

            $review->setUserId(intval($request->all()['currentUserId']));
            $review->setReview($request->all());

            $review->update($reviewId);

            $error = $review->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            $reviews = $review->getReviews();

            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'review' => $reviews[0],
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to preform update on review',
                        'error' => $e->getMessage()
                    ],
                    $e->getCode() ? $e->getCode() : 500
                );
        }
    }

    /**
     * Delete current user's review
     * @param Request $request
     * @param String $reviewId
     * @return JSONResponse
     */
    public function delete(Request $request, String $reviewId)
    {
        try {

            $review = new Review;
            $review->setUserId(intval($request->query('userId')));

            $review->delete($reviewId);

            $error = $review->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'success'
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to preform delete on review',
                        'error' => $e->getMessage()
                    ],
                    $e->getCode() ? $e->getCode() : 500
                );
        }
    }
}
