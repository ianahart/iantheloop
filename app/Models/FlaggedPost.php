<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlaggedPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pos_id',
        'reasons'
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
}
