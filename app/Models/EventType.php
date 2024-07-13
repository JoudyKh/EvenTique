<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        ];

    public function events(){
        return $this->hasMany(Event::class , 'event_type_id');
    }
    public function companies(){
        return $this->hasMany(Company::class , 'event_type_id');
    }
    public function packages(){
        return $this->hasMany(Package::class , 'event_type_id');
    }
    public function orders(){
        return $this->hasMany(Order::class , 'event_type_id');
    }
}
