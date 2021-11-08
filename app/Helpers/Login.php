<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use App\Helpers\Status;
use App\Events\UserStatusChanged;
use Exception;



class Login
{
  private array $credentials;
  private array $token;
  private ?string $exception;

  public function setCredentials(array $credentials)
  {
    $this->credentials = $credentials;
  }

  public function getException()
  {
    return isset($this->exception) ? $this->exception : NULL;
  }

  public function getToken()
  {
    return $this->token;
  }
  /**
   * Login user if they are authenticated with valid credentials
   * @param void
   * @return Int
   */
  public function loginUser()
  {
    try {

      $user = User::where('email', '=', $this->credentials['email'])
        ->first();

      if (!$user) {
        throw new Exception('Sorry, we couldn\'t find an account with that email.');
      }

      if (Hash::check($this->credentials['password'], $user->password) && Auth::attempt($this->credentials)) {
        $this->token = [
          'access_token' => $user->createToken('auth_token')->plainTextToken,
          'token_type' => 'Bearer',
          'user_info' => json_encode($user->getUserInfo()),
        ];

        $this->updateStatus();

        broadcast(new UserStatusChanged($user));

        return $user->setting->id;
      } else {
        throw new Exception('The provided credentials are invalid.');
      }
    } catch (Exception $e) {

      $this->exception = $e->getMessage();
    }
  }
  /**
   * update authentication column
   * @param void
   * @return void
   */
  private function updateStatus()
  {
    $userStatus = new Status(Auth::user()->id);

    $userStatus->updateStatus(true, 'online');
  }


  /**
   * retrieve user's profile picture
   * @param Void
   * @return String
   */
  private function getProfilePic()
  {
    if (Auth::user()->profile_created) {

      $profile = Profile::where('user_id', '=', Auth::user()->id)->first();

      return $profile->profile_picture;
    }
  }
}
