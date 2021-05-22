<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use HasFactory;


    protected $fillable = [
        'following',
        'followers',
        'following_count',
        'followers_count',
        'notifications',

    ];

    protected $casts = [
        'following' => 'array',
        'followers' => 'array',
        'notifications' => 'array',
    ];
}
