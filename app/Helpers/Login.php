<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Profile;
use App\Models\User;
use App\Helpers\Status;
use App\Events\UserStatusChanged;
use Exception;



class Login
{
  private array $credentials;
  private string $token;
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
   * @return void
   */
  public function loginUser()
  {
    try {

      $user = User::where('email', '=', $this->credentials['email'])
        ->first();
      if (!$user) {
        throw new Exception('Sorry, we couldn\'t find an account with that email.');
      }

      if (Hash::check($this->credentials['password'], $user->password)) {

        $TLL = 60;

        $payload = JWTAuth::attempt($this->credentials);

        $this->updateStatus();

        $this->token = $this->createNewToken($payload, $TLL, $user);

        broadcast(new UserStatusChanged($user));
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
    $userStatus = new Status(JWTAuth::user()->id);

    $userStatus->updateStatus(true, 'online');
  }


  /**
   * retrieve user's profile picture
   * @param Void
   * @return String
   */
  private function getProfilePic()
  {
    if (JWTAuth::user()->profile_created) {

      $profile = Profile::where('user_id', '=', JWTAuth::user()->id)->first();

      return $profile->profile_picture;
    }
  }

  /**
   * create new token with payload
   * @param string
   * @return string
   */
  private function createNewToken(string $payload)
  {
    $profile_pic = $this->getProfilePic();
    return json_encode([
      'access_token' => $payload,
      'profile_pic' => $profile_pic ?? '',
      'profile_created' => JWTAuth::user()->profile_created,
      'status' => 'online',
    ]);
  }
}
