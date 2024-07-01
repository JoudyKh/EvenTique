<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'company_name',
        'registration_number',
        'location',
        'city',
        'country',
        'days',
        'hours_from',
        'hours_to',
        'description',
        'accept_privacy',
        'event_type_id'
    ];
}
