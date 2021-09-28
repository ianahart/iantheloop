<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    protected $table = 'searches';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'formatted_search_value' => '',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'search_value',
        'formatted_search_value',
        'created_in_unix',
        'purge_in_unix'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'boolean',
        'search_value' => 'string',
        'formatted_search_value' => 'string',
        'created_in_unix' => 'integer',
        'purge_in_unix' => 'integer',
    ];

    /**
     * capitalize the search value.
     *
     * @param  string  $value
     * @return void
     */
    public function setFormattedSearchValueAttribute($value)
    {
        $this->attributes['formatted_search_value'] = ucwords($value);
    }

    public function searcherUser()
    {
        return $this->belongsTo(User::class, 'searcher_user_id');
    }

    public function searchedUser()
    {
        return $this->belongsTo(User::class, 'searched_user_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
