<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Profile;
use App\Helpers\AmazonS3;
use App\Http\Requests\StoreMultipleForm;


class ProfileController extends Controller
{
    /*
     * Create user profile
     * @param Request $request
     * @return JsonResponse
     */

    private int $userId;
    private array $data;
    public string $profilePic;

    public function store(StoreMultipleForm $request)
    {

        try {

            $validated = $request->validated();

            if ($validated) {

                $this->restructureFormData($request->all());

                $token = $request->header('Authorization');

                $this->getUserIdFromToken($token);

                $this->storeProfile();

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
     * @return null or string
     */

    private function fileValue(mixed $file)
    {

        $fileURL = '';

        if (isset($file)) {

            $fileName = $file->getClientOriginalName();
            $fileURL = $this->getFileURL($file, $fileName);
        }
        return $fileURL;
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

        $backgroundURL = $this->fileValue($backgroundFile);
        $profileURL = $this->fileValue($profileFile);

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

                    $profile->$key = strtolower($value);
                }
            } else if (str_contains($key, 'url-')) {

                $links[] = strtolower($this->data[$key]);
            }
        }

        $this->profilePic = $profileURL;

        $profile->links = json_encode($links);
        $profile->user_id = $this->userId;
        $profile->profile_picture = $profileURL;
        $profile->background_picture = $backgroundURL;

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
}
