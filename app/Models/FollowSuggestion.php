<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'user_id',
        'prospect_user_id',
        'unique_identifier',
        'mutual_follows',
        'rejected',
        'rejected_at'
    ];


    protected $casts = [
        'profile_id' => 'integer',
        'user_id' => 'integer',
        'prospect_user_id' => 'integer',
    ];

    public function prospect()
    {
        return $this->belongsTo(User::class, 'prospect_user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
