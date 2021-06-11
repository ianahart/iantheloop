<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_text',
        'photo_filename',
        'video_filename',
        'photo_link',
        'video_link',
        'like_records',
        'likes',
        'comments_count',
    ];


    protected $casts = [
        'like_records' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }

    public function postLikes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }
}
