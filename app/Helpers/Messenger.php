<?php

namespace App\Helpers;

use Exception;
use App\Models\User;

class Messenger
{

  private int $curUserId;
  private string $error;
  private object $contacts;

  public function __construct($curUserId)
  {
    $this->curUserId = $curUserId;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function getContacts()
  {
    return $this->contacts;
  }

  public function aggregateContacts()
  {

    try {

      $currentUser = User::find($this->curUserId);

      if (is_null($currentUser->stat->following) || is_null($currentUser->stat->followers)) {

        throw new Exception('No contacts available yet');
      }


      $this->contacts =  User::whereIn(
        'id',
        array_intersect(
          array_keys($currentUser->stat->following),
          array_keys($currentUser->stat->followers)
        )
      )
        ->with(
          [
            'stat',
            'profile'
          ]
        )
        ->get();

      foreach ($this->contacts as $key => $contact) {

        $this->capitalize($contact);
      }
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
  * Capitalize words
  * @param object
  * @return string
  */
  private function capitalize(object $contact)
  {

    $contact->formatted_name = implode(
      ' ',
      array_map(
        function ($char) {
          return strtoupper(
            substr($char, 0, 1)
          ) . strtolower(substr($char, 1, strlen($char) - 1));
        },
        explode(' ', $contact->full_name)
      )
    );
  }
}
