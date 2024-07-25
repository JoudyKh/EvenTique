<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHours extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'hours_from',
        'hours_to',
        'company_id'
    ];

    public $translatable = [
        'day'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
