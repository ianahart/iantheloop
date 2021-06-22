<?php

namespace App\Helpers;

use App\Models\LoginAttempt;
use Exception;

class LoginThrottle
{
  private int $createdSeconds;
  private string $clientIp;
  private string $userAgent;
  private string $error;


  public function setCreatedSeconds($createdSeconds)
  {
    $this->createdSeconds = $createdSeconds;
  }

  public function setClientIp($clientIp)
  {
    $this->clientIp = $clientIp;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }

  // check count
  // insert
  // delete
  /*
  *Insert a login attempt to the login_attempts table
  *@param void
  *@return void
  */
  public function recordLoginAttempt()
  {
    try {

      $loginAttempt = new LoginAttempt();

      $loginAttempt->created_seconds = $this->createdSeconds;
      $loginAttempt->user_agent = $this->userAgent;
      $loginAttempt->ip_address = $this->clientIp;

      $loginAttempt->save();
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }

  /*
  *count how many previous login attempts have been made in the past 15 minutes by the client
  *with matching ip_address & user_agent
  *@param void
  *@return bool
  */
  public function countLoginAttempts()
  {

    try {

      $maxAttempts = 5;

      $numOfAttempts = LoginAttempt::where('ip_address', '=', $this->clientIp)
        ->where('user_agent', '=', $this->userAgent)
        ->count();

      return $numOfAttempts >= $maxAttempts ? true : false;
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  public function checkTimePassed()
  {
    try {
      $threshold = 900;
      $timeElapsed = time() - $threshold;

      $latestAttempt = LoginAttempt::where('ip_address', '=', $this->clientIp)
        ->where('user_agent', '=', $this->userAgent)
        ->latest()
        ->first();

      return $latestAttempt->created_seconds < $timeElapsed ? true : false;
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
  *after 15 minutes are up remove all the attempts related to the client
  *@param void
  *@return void
  */
  public function clearLoginAttempts()
  {
    try {

      $attempts = LoginAttempt::where('user_agent', '=', $this->userAgent)
        ->where('ip_address', '=', $this->clientIp)
        ->get();

      foreach ($attempts as $attempt) {

        $attempt->delete();
      }
    } catch (Exception $e) {
      $this->error = $e->getMessage();
    }
  }
}
