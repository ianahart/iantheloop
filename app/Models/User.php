<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;




class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'password',
    ];

    /**
     * Get the user's formatted full name.
     *
     * @param  string  $value
     * @return string
     */
    public function getFullNameAttribute($value)
    {
        return ucwords($value);
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'notifications.' . $this->id;
    }


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_created' => 'boolean',
    ];

    /*
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /*
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */

    public function getJWTCustomClaims()
    {
        return [
            'user_id' => $this->id,
            'profile_created' => $this->profile_created,
            'profile_pic' => $this->profile->profile_picture ?? '',
            'name' => $this->full_name,
            'status' => 'online',
            'is_logged_in' => true,
            'slug' => $this->slug,
            'user_settings_user_id' => $this->setting->user_id ?? null,
            'settings_id' => $this->setting->id ?? null,
        ];
    }

    /**
     * Get the profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function stat()
    {
        return $this->hasOne(Stat::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'subject_user_id');
    }

    public function authorPosts()
    {
        return $this->hasMany(Post::class, 'author_user_id');
    }

    public function postLikes()
    {
        return $this->hasMany(PostLike::class, 'user_id');
    }

    public function flaggedPosts()
    {

        return $this->hasMany(FlaggedPost::class, 'user_id');
    }

    public function comments()
    {

        return $this->hasMany(Comment::class, 'user_id');
    }


    public function commentLikes()
    {
        return $this->hasMany(CommentLike::class, 'user_id');
    }

    public function followRequests()
    {
        return $this->hasMany(FollowRequest::class, 'requester_user_id');
    }

    public function followReceives()
    {
        return $this->hasMany(FollowRequest::class, 'receiver_user_id');
    }

    public function recipientMessages()
    {
        return $this->hasMany(Message::class, 'recipient_user_id');
    }

    public function senderMessages()
    {
        return $this->hasMany(Message::class, 'sender_user_id');
    }

    public function recipientFollowSuggestion()
    {
        return $this->hasMany(FollowSuggestion::class, 'user_id');
    }

    public function prospectFollowSuggestion()
    {
        return $this->hasMany(FollowSuggestion::class, 'prospect_user_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'user_id');
    }

    public function subjectStories()
    {
        return $this->hasMany(Story::class, 'user_id');
    }

    public function subjectStory()
    {
        return $this->hasOne(Story::class, 'user_id');
    }

    public function authorStories()
    {
        return $this->hasMany(Story::class, 'story_user_id');
    }

    public function searchers()
    {
        return $this->hasMany(Search::class, 'searcher_user_id');
    }

    public function searched()
    {
        return $this->hasMany(Search::class, 'searched_user_id');
    }

    public function setting()
    {
        return $this->hasOne(Setting::class, 'user_id');
    }

    public function blockedList()
    {
        return $this->hasMany(Privacy::class, 'blocked_by_user_id');
    }

    public function blockedByList()
    {
        return $this->hasMany(Privacy::class, 'blocked_user_id');
    }
}
