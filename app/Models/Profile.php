<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'user_id',
        'birth_day',
        'birth_month',
        'birth_year',
        'display_name',
        'town',
        'state',
        'country',
        'phone',
        'links',
        'bio',
        'relationship',
        'interests',
        'company',
        'position',
        'work_city',
        'description',
        'month_from',
        'year_from',
        'month_to',
        'year_to',
        'profile_picture',
        'profile_filename',
        'background_picture',
        'background_filename',
        'work_currently'
    ];

    protected $casts = [
        'links' => 'array',
        'interests' => 'array',
        'work_currently' => 'boolean',
        'user_id' => 'integer'
    ];


    /**
     * Get the formatted company.
     *
     * @param  void;
     * @return string
     */
    public function getCompanyAttribute()
    {

        return implode(' ', array_map(
            function ($word) {
                return strtoupper(substr($word, 0, 1)) . strtolower(substr($word, 1));
            },
            explode(' ', $this->attributes['company'])
        ));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stat()
    {
        return $this->hasOne(Stat::class);
    }

    public function followSuggestion()
    {
        return $this->hasOne(FollowSuggestion::class);
    }

    public function story()
    {
        return $this->hasOne(Story::class);
    }

    public function profile()
    {
        return $this->hasOne(Search::class);
    }
}
