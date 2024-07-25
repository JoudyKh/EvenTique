<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'price',
        'description',
        'discounted_packages',
        'activation',
        'category_id',
        'company_id',
    ];
    public $translatable = [
        'name',
        'description',
    ];
    public function favorites(){
        return $this->belongsTo(Favorite::class);
    }

    public function events(){
        return $this->belongsToMany(Event::class , 'event_service', 'service_id', 'event_id');
    }

    public function packages(){
        return $this->belongsToMany(Package::class , 'package_service_pivot');
    }

    public function reviews(){
        return $this->hasMany(Review::class , 'service_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'model');
    }
}
