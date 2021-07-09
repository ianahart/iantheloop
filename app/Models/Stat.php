<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use HasFactory;


    protected $fillable = [
        'following',
        'profile_id',
        'user_Id',
        'followers',
        'following_count',
        'followers_count',
        'notifications',

    ];


    protected $casts = [
        'following' => 'array',
        'followers' => 'array',
        'notifications' => 'array',
        'profile_id' => 'integer',
        'user_id' => 'integer',
        'following_count' => 'integer',
        'followers_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
