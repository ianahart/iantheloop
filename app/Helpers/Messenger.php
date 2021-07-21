<?php

namespace App\Helpers;

use App\Events\MessageSent;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use Exception;
use DateTime;
use DateTimeZone;





class Messenger
{

  private int $curUserId;
  private string $error;
  private object $contacts;
  private $newMessage;
  private array $chatMessages;

  public function __construct(int $curUserId)
  {
    $this->curUserId = $curUserId;
  }

  public function getError()
  {
    return isset($this->error) ? $this->error : NULL;
  }

  public function setNewMessage($newMessage)
  {
    $this->newMessage = $newMessage;
  }

  public function getContacts()
  {
    return $this->contacts;
  }

  public function getNewMessage()
  {
    return $this->newMessage;
  }

  public function getChatMessages()
  {
    return $this->chatMessages;
  }

  private function makeReadableDate()
  {
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone('America/New_York'));

    return $date->format('g:ia m/j/Y');
  }

  public function storeNewMessage()
  {
    try {

      $this->newMessage = array_merge(
        [],
        $this->newMessage['recipient'],
        $this->newMessage['sender']
      );

      $message = new Message();

      $currentUser = User::find($this->curUserId);

      foreach ($this->newMessage as $key => $value) {
        $message[$key] = $value;
      }

      $message->message_sent = $this->makeReadableDate();


      $existingConversation = Message::whereIn(
        'recipient_user_id',
        [$message->sender_user_id, $message->recipient_user_id]
      )
        ->whereIn(
          'sender_user_id',
          [$message->sender_user_id, $message->recipient_user_id]
        )
        ->first();

      if (is_null($existingConversation)) {

        $message['conversation_id'] = Str::uuid();
      } else {

        $message['conversation_id'] = $existingConversation->conversation_id;
      }

      $message->save();

      $message['profile_picture'] = $currentUser->profile->profile_picture;

      $this->capitalize($currentUser);

      $message['sender_name'] = $currentUser->formatted_name;

      broadcast(new MessageSent($message, $currentUser));
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
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
        ->orderBy('status', 'DESC')
        ->get();

      foreach ($this->contacts as $key => $contact) {

        $this->capitalize($contact);
      }
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  public function aggregateChatMessages(string $recipientId)
  {

    try {


      $sixMonthsAgo = time() - (264289 * 60);

      $sixMonthsAgo = Carbon::createFromTimestamp($sixMonthsAgo);
      $sixMonthsAgo = Carbon::createFromFormat('Y-m-d H:i:s', $sixMonthsAgo);

      $results = Message::OrderBy('messages.created_at', 'DESC')
        ->whereIn('recipient_user_id', [$recipientId, $this->curUserId])
        ->whereIn('sender_user_id', [$this->curUserId, $recipientId])
        ->join('users', 'messages.sender_user_id', 'users.id')
        ->join('profiles', 'messages.sender_user_id', '=', 'profiles.user_id')
        ->select('messages.*',  'profiles.profile_picture')
        ->get();

      $this->chatMessages = $results->count() === 0 ? [] : $results->toArray();
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
