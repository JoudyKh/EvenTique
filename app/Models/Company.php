<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Company extends Model
{
    use HasFactory, HasTranslations;

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

    public $translatable = [
        'location',
        'city',
        'country',
        'description',
    ];
    static $trsnalatableAtt = [
        'location',
        'city',
        'country',
        'description',
    ];
    public function categories(){
        return $this->belongsToMany(Category::class , 'category_company_pivot');
    }
}
