<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherRecord extends Model
{
    use HasFactory;

    protected $table = 'weather_data';

    protected $fillable = [
        'temperature',
        'humidity',
        'pressure',
        'wind_speed',
        'description',
        'icon',
        'created_at',
    ];

    public $timestamps = false;
}
