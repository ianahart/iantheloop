<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privacy extends Model
{
    use HasFactory;

    protected $table = 'privacies';


    /**
     * The attributes that are mass assignable for Privacy.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'created_at',
        'setting_id',
        'blocked_user_id',
        'blocked_by_user_id',
        'profile_id',
        'stat_id',
        'blocked_profile',
        'blocked_messages',
        'blocked_stories',
        'blocked_profile_duration',
        'blocked_messages_duration',
        'blocked_stories_duration',
        'created_in_unix',
    ];

    /**
     * The attributes that should be cast to native types for Privacy.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'setting_id' => 'integer',
        'blocked_user_id' => 'integer',
        'blocked_by_user_id' => 'integer',
        'profile_id' => 'integer',
        'stat_id' => 'integer',
        'blocked_profile' => 'boolean',
        'blocked_messages' => 'boolean',
        'blocked_stories' => 'boolean',
        'blocked_profile_duration' => 'string',
        'blocked_messages_duration' => 'string',
        'blocked_stories_duration' => 'string',
        'created_in_unix' => 'integer',
        'stat_id' => 'integer',
        'stat_id' => 'integer',
        'stat_id' => 'integer',
        'stat_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stat()
    {
        return $this->belongsTo(Stat::class, 'stat_id');
    }

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }
}
