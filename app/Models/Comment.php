<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'comment_text',
        'likes',
        'is_edited',
    ];

    protected $casts = [

        'user_id' => 'integer'
    ];


    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function post()
    {

        return $this->belongsTo(Post::class);
    }

    public function commentLikes()
    {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }
}
