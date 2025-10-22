<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'fish',
        'fakultet',
        'guruh',
        'telefon',
        'tyutori',
        'hudud',
        'manzil',
        'url_manzil',
    ];
}
