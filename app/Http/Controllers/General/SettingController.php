<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlockSearchRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Setting;
use App\Models\User;
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

            $setting = new Setting;
            $setting->setCurrentUserId(intval($request->all()['current_user_id']));

            $setting->makeUserSettings();

            $error = $setting->getError();

            if (!is_null($error)) {
                throw new Exception($error);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Success',
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

            $setting = new Setting;
            $setting->setCurrentUserId(intval($currentUserId));

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

            $setting = new Setting;
            $setting->setCurrentUserId(intval($request->all()['current_user_id']));

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
            $setting = new Setting;
            $setting->setCurrentUserId(intval($userId));

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

            $setting = new Setting;
            $setting->setCurrentUserId(intval($request->all()['current_user_id']));

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

            $setting = new Setting;
            $setting->setCurrentUserId(intval($request->query('userId')));

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

    /**
     * Turn on/off the remember me feature
     * @param Request
     * @param String
     * @return JsonResponse
     */
    public function updateRememberMe(Request $request, String $settingId)
    {

        try {

            $data = array_merge(
                $request->all(),
                [
                    'setting_id' => $settingId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );

            $setting = new Setting;
            $setting->setCurrentUserId(intval($data['current_user_id']));

            $cookie = $setting->updateRememberMe($data);

            $hasCookie = count($cookie) > 0 ? true : false;

            $exception = $setting->getException();

            if (count(array_values($exception)) > 0) {
                throw new Exception($exception['msg'], $exception['code']);
            }

            $response = response()
                ->json(
                    [
                        'msg' => 'Success',
                        'remember_me' => $hasCookie ? true : false,
                    ],
                    200
                );

            return $hasCookie ? $response
                ->withCookie($cookie['name'], $cookie['value'], $cookie['exp']) :
                $response->withCookie('remember_me');
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to turn on/off remember me feature. Please try again soon.',
                        'error' => $e->getMessage()
                    ],
                    $exception['code'] ?? 500
                );
        }
    }

    /**
     * return the current state (on/off) of the remember me feature
     * @param Request
     * @param String
     * @return JsonResponse
     */
    public function retrieveSecuritySettings(Request $request, String $settingId)
    {
        try {

            $setting = new Setting;
            $setting->setCurrentUserId(Auth::guard('sanctum')->user()->id);

            $securitySettings = $setting->retrieveSecuritySettings($settingId);

            $error = $setting->getError();
            if (!is_null($error)) {
                throw new Exception($error);
            }


            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'security_settings' => $securitySettings,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to load the state of your remember me status.',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }

    /**
     * Check to see if the remember me cookie is still valid
     * @param Request
     * @return JsonResponse
     */
    public function validateRememberMe(Request $request)
    {
        try {

            $setting = new Setting;

            $isUserValidated = $setting->validateRememberMe($request->userAgent());

            return response()
                ->json(
                    [
                        'msg' => 'Success', 'user_validated' => $isUserValidated,
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Your session has ended. Please login again...',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }

    /**
     * @param Request
     * @param String
     */
    public function updatePassword(UpdatePasswordRequest $request, String $settingId)
    {

        try {

            $request->validated();

            $setting = new Setting;

            $header = $request->header('Authorization');

            $token = str_replace('Bearer ', '', $header);

            $setting->updatePassword($request->all(), $settingId);

            $exception = $setting->getException();

            if (count(array_values($exception)) > 0) {
                throw new Exception($exception['msg'], $exception['code']);
            }
            $currentUser = User::find(Auth::guard('sanctum')->user()->id);
            $currentUser->tokens()->delete();

            return response()
                ->json(
                    [
                        'msg' => 'Success',
                        'data' => 'password updated'
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to update password.',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }

    /**
     * Delete User's Account
     * @param Request
     * @param String
     */
    public function deleteAccount(Request $request, String $settingId)
    {
        try {

            $setting = new Setting;

            $header = $request->header('Authorization');

            $token = str_replace('Bearer ', '', $header);


            $setting->deleteAccount($settingId, $token);

            $exception = $setting->getException();

            if (count(array_values($exception)) > 0) {
                throw new Exception($exception['msg'], $exception['code']);
            }
            return response()
                ->json(
                    [
                        'msg' => 'Account deleted'
                    ],
                    200
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to remove your account at this moment.',
                        'error' => $e->getMessage()
                    ],
                    $exception['code'] ?? 500
                );
        }
    }
}
