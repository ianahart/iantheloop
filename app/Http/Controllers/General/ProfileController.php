<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Http\Requests\StoreMultipleForm;


/*Get contents off the file*/

// $a = request()->file('backgroundfile');

// error_log(print_r($a->getClientOriginalName(), true));
class ProfileController extends Controller
{
    /*
     * Create user profile
     * @param Request $request
     * @return JsonResponse
     */
    private $userId;

    public function store(StoreMultipleForm $request)
    {


        try {





            $validated = $request->validated();

            if ($validated) {

                //---- GET ALL FORM DATA
                // error_log(print_r($request->all(), true));


                return response()->json(['msg' => 'hi'], 200);
            }




            // Uncomment this when all form validation is figured out

            // $token = $request->header('Authorization');

            // $this->getUserIdFromToken($token);

            // $status = $this->updateProfileCreated();

            // return response()->json(
            //     [
            //         'profileCreated' => $status,
            //         'msg' => 'success'
            //     ],
            //     200
            // );
        } catch (\Exception $e) {

            // error_log(print_r($e, true));
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
}
