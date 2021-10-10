<?php

namespace App\Helpers;

use App\Events\MessageSent;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Notifications\UnreadMessage;
use Illuminate\Support\Carbon;
use Exception;
use DateTime;
use DateTimeZone;


class Conversator
{

  const MESSAGE_LIMIT = 15;

  private int $curUserId;
  private string $error;
  private object $contacts;
  private $newMessage;
  private array $chatMessages = [];
  private int $conversationId;
  private array $metaData;

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

  public function setMetaData($metaData)
  {
    $this->metaData = $metaData;
  }

  public function getNewMessage()
  {
    return $this->newMessage;
  }

  public function getChatMessages()
  {
    return $this->chatMessages;
  }

  public function getConversationId()
  {
    return $this->conversationId;
  }

  private function makeReadableDate()
  {
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone('America/New_York'));

    return $date->format('g:ia m/j/Y');
  }

  /*
  *Stores a new Message and then broadcasts that message
  *@param int $conversationId
  */

  public function storeNewMessage(int $conversationId)
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

      $message->conversation_id = $conversationId;

      $message->save();

      $message['profile_picture'] = $currentUser->profile->profile_picture;

      $this->capitalize($currentUser);

      $message['sender_name'] = $currentUser->formatted_name;

      broadcast(new MessageSent($message, $currentUser));

      $unreadMessage = collect($message)
        ->filter(function ($value, $key) {
          if (in_array(
            $key,
            [
              'recipient_user_id',
              'sender_user_id',
              'recipient_name',
              'sender_name',
              'profile_picture'
            ]
          )) {
            return $value;
          }
        })->toArray();

      $recipient = User::find($unreadMessage['recipient_user_id']);
      $unreadMessage['status'] = $recipient->status;

      if (intval($recipient->cur_chat_window_user_id) !== intval($message['sender_user_id'])) {

        $recipient->notify(new UnreadMessage($unreadMessage));
      }

      $this->conversationId = $message->conversation_id;
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
      )->where(
        function ($query) use ($currentUser) {
          $query->whereNotIn(
            'id',
            $currentUser->blockedList
              ->where('blocked_messages', '=', 1)
              ->pluck('blocked_user_id')
          )->whereNotIn(
            'id',
            $currentUser->blockedByList
              ->where('blocked_messages', '=', 1)
              ->pluck('blocked_by_user_id')
          );
        }
      )->with(
        [
          'stat',
          'profile'
        ]
      )

        ->orderBy('status', 'DESC')
        ->get();

      $notifications = $this->countNotifications($currentUser);

      foreach ($this->contacts as $key => $contact) {

        $contact->unread_messages_count = array_key_exists($contact->id, $notifications) ?
          $notifications[$contact->id] : 0;

        $this->capitalize($contact);
      }
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  public function aggregateChatMessages(string $recipientId)
  {

    try {

      $structureOne = implode(' ', array_reverse(explode(' ', strval($this->curUserId) . ' ' . strval($recipientId))));
      $structureTwo = strval($this->curUserId) . ' ' . strval($recipientId);

      $conversations = Conversation::whereIn('participants', [$structureOne, $structureTwo])
        ->first();

      if (is_null($conversations)) {

        $this->createConversationRecord($recipientId);
        $this->chatMessages = ['total' => 0, 'chat_messages' => [], 'notifications_read' => false];
        return;
      } else {

        $sixMonthsAgo = time() - (264289 * 60);
        $sixMonthsAgo = Carbon::createFromTimestamp($sixMonthsAgo);
        $sixMonthsAgo = Carbon::createFromFormat('Y-m-d H:i:s', $sixMonthsAgo);

        $results = NULL;

        $query = Message::OrderBy('messages.created_at', 'DESC')
          ->orderBy('messages.id', 'DESC')
          ->whereIn('recipient_user_id', [$recipientId, $this->curUserId])
          ->whereIn('sender_user_id', [$this->curUserId, $recipientId]);

        if (
          empty($this->metaData['last_message']) && $this->metaData['last_message'] !== ''
          || empty($this->metaData['created_at'])
          && $this->metaData['last_message'] !== ''
        ) {

          $results = $query
            ->join('users', 'messages.sender_user_id', 'users.id')
            ->join('profiles', 'messages.sender_user_id', '=', 'profiles.user_id')
            ->select('messages.*',  'profiles.profile_picture')
            ->paginate(self::MESSAGE_LIMIT);
        } else {

          $results = $query
            ->where('messages.id', '<', $this->metaData['last_message'])
            ->join('users', 'messages.sender_user_id', 'users.id')
            ->join('profiles', 'messages.sender_user_id', '=', 'profiles.user_id')
            ->select('messages.*',  'profiles.profile_picture')
            ->paginate(self::MESSAGE_LIMIT);
        }

        $this->conversationId = $conversations->id;

        $this->chatMessages = [
          'total' => $results->toArray()['total'],
          'chat_messages' => $results->toArray()['data'],
          'notifications_read' => $this->markUnreadMessagesAsRead($recipientId),
        ];

        $curUser = User::find($this->curUserId);

        $curUser->cur_chat_window_user_id = intval($recipientId);
        $curUser->save();
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
  /*
  * Store a new Conversation record
  * @param string $recipientId
  * @return void
  */
  private function createConversationRecord($recipientId)
  {
    try {

      $conversation = new Conversation();

      $conversation->participants = strval($recipientId) . ' ' . strval($this->curUserId);

      $conversation->save();

      $conversation = $conversation->refresh();
      $this->conversationId = $conversation->id;
    } catch (Exception $e) {

      $this->error = $e->getMessage();
    }
  }

  /*
  * Count current user's notifications by sender
  * @param User $user
  * @return array;
  */

  private function countNotifications(User $user)
  {
    $data = $user->notifications()
      ->where('type', '=', 'App\Notifications\UnreadMessage')
      ->whereNull('read_at')
      ->pluck('data');

    $notifications = [];

    foreach ($data as $key => $value) {

      if (!array_key_exists($value['sender_user_id'], $notifications)) {
        $notifications[$value['sender_user_id']] = 1;
      } else {
        $notifications[$value['sender_user_id']] = $notifications[$value['sender_user_id']] + 1;
      }
    }
    return $notifications;
  }

  /*
  * Mark unread messages of the current user from the recipient
  * @param string $recipientId
  * @return void
  */
  private function markUnreadMessagesAsRead(string $recipientId)
  {

    $currentUser = User::find($this->curUserId);

    $notifications = $currentUser
      ->notifications()
      ->where('type', '=', 'App\Notifications\UnreadMessage')
      ->whereIn('data->sender_user_id', [$recipientId])
      ->whereIn('data->recipient_user_id', [$this->curUserId])
      ->update(['read_at' => now()]);

    return $notifications > 0 ? true : false;
  }
}
