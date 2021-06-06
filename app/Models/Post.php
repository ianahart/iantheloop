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
        'likes',
        'comments_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }
}
