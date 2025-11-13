<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherReading extends Model
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
        'observed_at',
        'user_id',
    ];

    protected $casts = [
        'temperature' => 'float',
        'humidity' => 'integer',
        'pressure' => 'integer',
        'wind_speed' => 'float',
        'observed_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
