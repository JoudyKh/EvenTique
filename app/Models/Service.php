<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'discription',
        'discounted_packages',
        'activation',
        ];
    public $translatable = ['name'];
    public function favorites(){
        return $this->belongsTo(Favorite::class);
    }

    public function events(){
        return $this->belongsToMany(Event::class , 'event_service_pivot');
    }

    public function packages(){
        return $this->belongsToMany(Package::class , 'package_service_pivot');
    }
}
