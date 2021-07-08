<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_user_id',
        'author_user_id',
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
        'subject_user_id' => 'integer',
        'author_user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }

    public function postLikes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function flaggedPosts()
    {

        return $this->hasMany(FlaggedPost::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
}
