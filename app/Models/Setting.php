<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    /**
     * The attributes that are mass assignable for Setting.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'created_at',
        'user_id',
        'remember_me',
        'block_profile_on',
        'block_messages_on',
        'block_stories_on',
    ];

    /**
     * The attributes that should be cast to native types for Setting.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'remember_me' => 'boolean',
        'block_profile_on' => 'boolean',
        'block_messages_on' => 'boolean',
        'block_stories_on' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function privacies()
    {
        return $this->hasMany(Privacy::class, 'setting_id');
    }
}
