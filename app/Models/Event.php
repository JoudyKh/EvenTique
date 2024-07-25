<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'date',
        'time',
        'budget',
        'guests',
        'status',
        'event_type_id',
        'user_id',
    ];
    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
