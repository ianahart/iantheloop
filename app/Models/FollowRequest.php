<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowRequest extends Model
{
    use HasFactory;


    protected $fillable = [
        'requester_user_id',
        'receiver_user_id',
        'request_date_sent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }
}
