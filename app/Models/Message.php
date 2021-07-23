<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_user_id',
        'sender_user_id',
        'recipient_name',
        'sender_name',
        'message',
        'conversation_id'
    ];

    protected $casts = [
        'recipient_user_id' => 'integer',
        'sender_user_id' => 'integer',
        'conversation_id' => 'integer',
    ];

    public function recipientUser()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }


    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Message::class, 'conversation_id');
    }
}
