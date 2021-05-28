<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Profile;
use App\Helpers\AmazonS3;
use App\Helpers\Statistics;
use App\Http\Requests\StoreMultipleForm;
use App\Http\Requests\EditProfileRequest;
use App\Models\Stat;
use Exception;
use stdClass;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{

    private int $userId;
    private array $data;
    public string $fullName;
    public string $profilePic;
    public array $editedData;
    public bool $isUpdated;
    public string $updatedProfilePic;

    /*
     * Get base profile data
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        try {

            $profileExists = User::where('id', $id)->value('profile_created');

            if (!$profileExists) {

                throw new Exception();
            }

            $profile = Profile::where('user_id', '=', $id)
                ->select(
                    [
                        'id',
                        'user_id',
                        'company',
                        'position',
                        'display_name',
                        'profile_picture',
                        'background_picture',
                    ]
                )
                ->first();

            if (!empty($profile)) {

                $this->fullName = $this->getFullName($id);

                $profile->full_name = $this->fullName;

                $profile = $this->capitalizeColumns($profile->getAttributes(), ['bio']);
            }

            $stat = Stat::where('user_id', '=', $id)->first();

            $currentUserId = JWTAuth::user()->id;
            $currentUser = new stdClass();
            $currentUser->user_id = $currentUserId;


            $statisticInst = new Statistics($currentUser, $stat);

            $statisticInst->checkCurrUserFollowing();

            $currUserFollowing = $statisticInst->getUserIsFollowing();

            if (!$currUserFollowing) {

                $currUserFollowing = false;
            }

            return response()->json(
                [
                    'msg' => 'success',
                    'profile' => $profile,
                    'stats' => $stat,
                    'currUserFollowing' => $currUserFollowing,
                ],
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'msg' => 'Profile for user not found',
                    'error' => $e->getMessage()
                ],
                404
            );
        }
    }

    /*
     * Capitalize columns that need it
     * @param array $data
     * @param array $excludes
     * @return JsonResponse
     */
    private function capitalizeColumns(array $data, array $excludes)
    {

        $array = [];

        array_walk($data, function ($val, $key) use (&$array, $excludes) {

            if (isset($val)) {

                $array[$key] = preg_match('/^https?:\/\//', $val)
                    || in_array($key, $excludes) ? $val : ucwords($val);
            }
        });
        return $array;
    }

    /*
     * Get user full name
     * @param string $userId
     * @return string
     */
    private function getFullName(string $userId)
    {
        $user = User::where('id', '=', $userId)->first();

        return $user->full_name;
    }

    /*
     * Get all of profile data
     * @param string $profileId
     * @return JsonResponse
     */
    public function showAbout(string $profileId)
    {
        try {

            $profileData = Profile::findOrFail($profileId);

            $profileData = $profileData->getAttributes();

            $profileData['full_name'] = $this->getFullName($profileData['user_id']);

            $formattedAboutData = $this->formatAboutData($profileData);

            return response()->json(
                [
                    'msg' => 'success',
                    'profile' => $formattedAboutData,
                ],
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'msg' => 'Profile details were not found',
                    'error' => $e->getMessage()
                ],
                404
            );
        }
    }

    /*
     * Punctuate paragraph text
     * @param string
     * @return string
     */
    private function punctuateParagraph(string $paragraph)
    {
        $words = explode(' ', strtolower(trim($paragraph)));

        $punctuated = [];

        for ($i = 0; $i < count($words); $i++) {

            if (str_ends_with($words[$i], '.')) {

                if ($i === count($words) - 1) {

                    array_push($punctuated, $words[$i]);
                } else if ($i < count($words)) {

                    $start = strtoupper(
                        substr($words[$i + 1], 0, 1)
                    ) . substr($words[$i + 1], 1);

                    array_push($punctuated,  $words[$i], $start);
                }
            } else {

                array_push($punctuated,  $words[$i]);
            }
        }

        foreach ($punctuated as $key => $word) {

            if ($key < count($punctuated) - 1 && str_ends_with($punctuated[$key], '.')) {

                $firstChar = substr($punctuated[$key + 1], 0, 1);

                if ($firstChar === ucfirst($firstChar)) {

                    array_splice($punctuated, $key + 2, 1);
                }
            }
        }

        $punctuated[0] = ucfirst($punctuated[0]);

        return implode(' ', $punctuated);
    }

    /*
     * Format about data
     * @param array
     * @return array;
     */
    private function formatAboutData(array $data)
    {
        $data = $this->capitalizeColumns($data, ['bio', 'description']);

        $data['interests'] = array_map(
            function ($interest) {
                $interest['name'] = ucwords($interest['name']);
                return $interest;
            },
            json_decode(json_decode($data['interests'], true), true)
        );

        $data['links'] = json_decode(json_decode($data['links'], true));

        $data['bio'] = $this->punctuateParagraph($data['bio']);

        $data['description'] = $this->punctuateParagraph($data['description']);

        return $data;
    }

    /*
     * Create user profile
     * @param Request $request
     * @return JsonResponse
     */
    public function store(StoreMultipleForm $request)
    {

        try {

            $validated = $request->validated();

            if ($validated) {

                $this->restructureFormData($request->all());

                $token = $request->header('Authorization');

                $this->getUserIdFromToken($token);

                $this->storeProfile();

                $userFullName = User::where('id', '=', $this->userId)->pluck('full_name');

                $stat = new Stat();

                $profileId = Profile::where('user_id', '=', $this->userId)->pluck('id');

                $stat->user_id = $this->userId;
                $stat->profile_id = $profileId[0];
                $stat->name = $userFullName[0];

                $stat->save();

                $status = $this->updateProfileCreated();

                return response()->json(
                    [
                        'profileCreated' => $status,
                        'msg' => 'success',
                        'profile_pic' => $this->profilePic,
                    ],
                    200
                );
            }
        } catch (\Exception $e) {

            return response()->json(
                [
                    'msg' => $e->getMessage()
                ],
                409
            );
        }
    }

    /*
     * get the user's id from token
     * @param string $token
     * @return void
     */
    private function getUserIdFromToken(string $token)
    {
        $payload = explode('.', $token)[1];

        $decoded = json_decode(base64_decode($payload));

        $this->userId = $decoded->sub;
    }

    /*
     * add  the user supplied form data to user profile
     * @params mixed $file
     * @return array
     */
    private function fileValue(mixed $file)
    {

        $fileURL = '';
        $fileName = '';
        if (isset($file)) {

            $fileName = uniqid() . $file->getClientOriginalName();
            $fileURL = $this->getFileURL($file, $fileName);
        }
        return [
            $fileURL,
            $fileName
        ];
    }

    /*
     * add  the user supplied form data to user profile
     * @void
     * @return void
     */
    private function storeProfile()
    {
        $backgroundFile = $this->data['backgroundfile'];
        $profileFile = $this->data['profilefile'];

        [$backgroundURL, $backgroundFileName] = $this->fileValue($backgroundFile);
        [$profileURL, $profileFileName] = $this->fileValue($profileFile);


        $profile = new Profile();

        $excludeFields = ['backgroundfile', 'profilefile'];

        $links = [];

        foreach ($this->data as $key => $value) {

            if (!str_contains($key, 'url-') && !in_array($key, $excludeFields)) {

                if (is_array($this->data[$key])) {

                    $encodedArray = json_encode(
                        array_map(function ($item) {

                            $item['name'] = strtolower($item['name']);

                            return $item;
                        }, $this->data[$key])
                    );

                    $profile->$key = $encodedArray;
                } else {

                    if (gettype($value) !== 'boolean') {

                        $profile->$key = strtolower($value);
                    } else {

                        $profile->$key = $value;
                    }
                }
            } else if (str_contains($key, 'url-')) {

                $links[] = strtolower($this->data[$key]);
            }
        }

        $this->profilePic = $profileURL;

        $profile->links = json_encode($links);
        $profile->user_id = $this->userId;
        $profile->profile_picture = $profileURL;
        $profile->profile_filename = $profileFileName;
        $profile->background_picture = $backgroundURL;
        $profile->background_filename = $backgroundFileName;

        $profile->save();
    }

    /*
     * update user's profile created status to true
     * @param void
     * @return boolean
     */
    private function updateProfileCreated()
    {

        $user = User::find($this->userId);

        $user->profile_created = true;

        $user->save();

        return true;
    }


    /*
     * merge form arrays into a single array
     * @param array $request
     * @return void
     */
    private function restructureFormData(array $request)
    {

        $form = [];
        $files = [];

        foreach ($request as $key => $validation) {

            if (is_array($validation)) {

                $form = array_merge(
                    $form,
                    $validation
                );
            } else {

                $files[$key] = $validation ?? null;
            }
        }

        $form = array_merge(
            $form,
            $files
        );

        unset($form['data']);

        $this->data = $form;
    }

    /*
     * upload file to s3 and retrieve the file's public URL
     * @param object $file string $fileName
     * @return string
     */
    private function getFileURL(object $file, string $fileName)
    {

        $s3Instance = new AmazonS3($fileName, $file);

        $s3Instance->uploadToBucket();

        $fileURL = $s3Instance->downloadFromBucket();

        return $fileURL;
    }

    /*
     * Get all of profile data
     * @param string $profileId
     * @return JsonResponse
     */
    public function edit(string $profileId)
    {

        try {

            $profile = Profile::where('id', '=', $profileId)->first();

            if ($profile->user_id !== JWTAuth::user()->id) {

                throw new Exception('User not allowed to edit another user\'s profile');
            }

            $profile->interests = json_decode($profile->interests, true);
            $profile->links = json_decode($profile->links, true);

            return response()->json(
                [
                    'msg' => 'success',
                    'data' => $profile,
                ],
                200
            );
        } catch (Exception $e) {

            return response()->json(
                [
                    'msg' => 'error',
                    'intercept' => false,
                    'error' => $e->getMessage()
                ],
                401
            );
        }
    }

    /*
     * Get all of profile data
     * @param EditProfileRequest $request
     * @param string $profileId
     * @return JsonResponse
     */
    public function update(EditProfileRequest $request, string $profileId)
    {
        try {

            $validated = $request->validated();

            if ($validated) {

                $this->editedData = $request->all();
                $exclude = ['data', '_method', 'background_pic_prev', 'profile_pic_prev'];

                $this->removeMetaData($exclude);
                $this->sanitizeEditedData();

                $profile = Profile::find($profileId);
                $this->makeUpdates($profile);
            }


            if ($this->isUpdated) {

                return response()
                    ->json(
                        [
                            'msg' => 'success',
                            'isUpdated' => $this->isUpdated,
                            'profile_pic' => $this->updatedProfilePic,
                        ],
                        200
                    );
            }
        } catch (Exception $e) {
            error_log(print_r($e->getMessage(), true));
            return response()
                ->json(
                    [
                        'msg' => 'Unable to update profile. Please try again soon.'
                    ],
                    400
                );
        }
    }

    /*
     * sanitize the edit request data
     * @param void
     * @param
     * @return JsonResponse
     */
    private function sanitizeEditedData()
    {
        $excluded = [
            'profile_picture',
            'background_picture',
            'profile_pic_prev',
            'background_pic_prev',
            'interests',
            'work_currently'
        ];
        $links = [];
        $urlKeys = [];

        foreach ($this->editedData as $key => $val) {

            if (!in_array($key, $excluded) && isset($val)) {

                if (str_contains($key, 'url-')) {

                    $links[] = $val;
                    $urlKeys[] = $key;
                }
                $this->editedData[$key] = strtolower(trim($val));
            }
        }

        $interests = $this->editedData['interests'];

        for ($i = 0; $i < count($interests); $i++) {
            $interests[$i]['name'] = strtolower(
                trim($interests[$i]['name'])
            );
        }

        for ($j = 0; $j < count($urlKeys); $j++) {
            unset($this->editedData[$urlKeys[$j]]);
        }

        $this->editedData['interests'] = json_encode($interests);
        $this->editedData['links'] = count($links) ? json_encode($links) : json_encode([]);
        $this->editedData['work_currently'] = $this->editedData['work_currently'] ? 1 : 0;
    }

    /*
     * remove fields that will not be inserted into DB
     * @param array $metaData
     * @return void
     */
    private function removeMetaData(array $metaData)
    {
        $this->editedData = array_filter(
            $this->editedData,
            function ($val, $key) use ($metaData) {

                if (!in_array($key, $metaData)) {

                    return $key;
                }
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /*
    * make updates to columns for the given profile
    * @param object $profile
    * @return void
    */
    // 2021-05-11 00:52:15
    private function makeUpdates(object $profile)
    {

        foreach ($this->editedData as $key => $val) {

            $routedValue = $this->routeValues($key, $val, $profile);

            if (is_array($routedValue)) {

                $routedValueKey = array_keys($routedValue)[0];

                if ($key === $routedValueKey) {

                    $profile->$key = $routedValue[$key][$key];
                    $prefix = explode('_', $routedValueKey)[0];
                    $fileName = $prefix . '_filename';
                    $profile->$fileName = $routedValue[$key][$fileName];
                }
            } else {

                $profile->$key = $routedValue;
            }
        }

        $this->updatedProfilePic = $profile->profile_picture;

        $profile->save();
        $this->isUpdated = true;
    }



    /*
    * make updates to columns for the given profile
    * @param string $key
    * @param mixed $value
    * @param object $profile
    * @return mixed (string, array)
    */

    private function routeValues(
        string $key,
        mixed $value,
        object $profile
    ) {
        $result = null;

        switch ($key) {

            case 'background_picture':

                if (isset($profile->background_filename)) {

                    $this->deletePrevFile($profile->background_filename);
                }

                [$backgroundURL, $backgroundFileName] = $this->fileValue($value);

                $result = [
                    $key => [
                        'background_picture' => $backgroundURL,
                        'background_filename' => $backgroundFileName
                    ]
                ];
                break;

            case 'profile_picture':

                if (isset($profile->profile_filename)) {

                    $this->deletePrevFile($profile->profile_filename);
                }

                [$profileURL, $profileFileName] = $this->fileValue($value);

                $result = [
                    $key => [
                        'profile_picture' => $profileURL,
                        'profile_filename' => $profileFileName
                    ]
                ];
                break;

            default:
                $result = $value;
        }
        return $result;
    }

    /*
    * delete previous file from amazon s3
    * @param string $fileName
    * @return void
    */
    private function deletePrevFile(string $fileName)
    {

        $s3Instance = new AmazonS3($fileName, null);
        $s3Instance->deleteFromBucket();
    }
}
