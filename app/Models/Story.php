<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Story extends Model
{
    use HasFactory;

    protected $table = 'stories';

    protected $fillable = [
        'id',
        'user_id',
        'story_user_id',
        'profile_id',
        'muted',
        'read_at',
        'photo_link',
        'photo_filename',
        'text',
        'created_at_unix',
        'expire_in_unix',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'story_user_id' => 'integer',
        'profile_id' => 'integer',
        'muted' => 'boolean',
        'created_at_unix' => 'integer',
        'expire_in_unix' => 'integer',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function authorUser()
    {
        return $this->belongsTo(User::class, 'story_user_id');
    }

    public function subjectUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
