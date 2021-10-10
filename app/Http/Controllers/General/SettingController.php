<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlockSearchRequest;
use App\Helpers\Setting;
use Illuminate\Http\Request;
use Exception;

class SettingController extends Controller
{
    /**
     * @param Request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {

            $setting = new Setting(intval($request->all()['current_user_id']));

            $updatedToken = $setting->makeUserSettings();

            $error = $setting->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'updated_token' => $updatedToken,
                    ],
                    201
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to create user\'s settings.',
                        'error' => $e->getMessage()
                    ],
                    500
                );
        }
    }

    /**
     * Get users who are following/followers who are not blocked
     * @param BlockSearchRequest
     * @return JsonResponse
     */
    public function search(BlockSearchRequest $request)
    {
        try {

            $request->validated();

            [$currentUserId, $type, $value] = array_values($request->all());

            $setting = new Setting(intval($currentUserId));

            $setting->incrementalSearch($type, $value);

            $error = $setting->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            $searches = $setting->getSearches();

            return response()->json(
                [
                    'msg' => 'Success',
                    'searches' => $searches,
                ],
                200
            );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to find search results.',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /**
     * Store a new blocked user to privacies table
     * @param Request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $setting = new Setting(intval($request->all()['current_user_id']));

            $setting->blockUser($request->all());

            $exception = $setting->getException();

            if (count(array_values($exception)) > 0) {
                throw new Exception($exception['msg'], $exception['code']);
            }
            return response()
                ->json(
                    [
                        'msg' => 'Success',
                    ],
                    201
                );
        } catch (Exception $e) {

            return response()->json(
                [
                    'msg' => 'Unable to add the user to block, something went wrong.',
                    'error' => $e->getMessage()
                ],
                $exception['code'] ?? 500
            );
        }
    }

    /**
     * retrieve the currently blocked users
     * @param Request
     * @param String $userId
     * @return JsonResponse
     */
    public function show(Request $request, String $userId)
    {
        try {
            $setting = new Setting(intval($userId));

            $setting->retrieveBlockedUsers();

            $error = $setting->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            $blockedUsers = $setting->getUsers();
            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'blocked_users' => $blockedUsers,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to retrieve your currently blocked users.',
                        'error' => $e->getMessage()
                    ],
                    404
                );
        }
    }

    /**
     * Update the blocked user's blocked columns
     * @param Request $request
     * @param String $privacyId
     * @return JsonResponse
     */
    public function updateBlockedUser(Request $request, String $privacyId)
    {

        try {

            $setting = new Setting(intval($request->all()['current_user_id']));

            $typesBlocked = $setting->updateBlockedUser($request->all());

            $exception = $setting->getException();

            if (count(array_values($exception)) > 0) {
                throw new Exception($exception['msg'], $exception['code']);
            }


            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'types_blocked_count' => $typesBlocked,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to update the provided blocked column',
                        'error' => $e->getMessage()
                    ],
                    $exception['code'] ?? 500
                );
        }
    }

    /**
     * @param Request
     * @param String $privacyId
     * @return JsonResponse
     */
    public function deleteBlockedUser(Request $request, String $privacyId)
    {
        try {

            $setting = new Setting(intval($request->query('userId')));

            $setting->deleteBlockedUser($privacyId);

            $exception = $setting->getException();

            if (count(array_values($exception)) > 0) {
                throw new Exception($exception['msg'], $exception['code']);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'data' => 'Blocked user unblocked'
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to delete the blockedUser',
                        'error' => $e->getMessage()
                    ],
                    $exception['code'] ?? 500
                );
        }
    }
}
