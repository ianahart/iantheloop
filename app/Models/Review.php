<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'text',
        'isEdited',
        'reviewed_at',
        'rating',
        'likes',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'rating' => 'integer',
        'likes' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
