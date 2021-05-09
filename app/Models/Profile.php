<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
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
        'background_picture',
    ];

    protected $casts = [
        'links' => 'array',
        'interests' => 'array',
    ];
}
