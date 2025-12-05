<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bounds extends Model
{
    protected $table = 'bounds';

    protected $fillable = [
        'min_lat',
        'max_lat',
        'min_lon',
        'max_lon',
        'temp_min',
        'temp_max',
        'salt_max',
        'salt_min',
        'w_min',
        'w_max',
        'rain_max',
        'rain_min',
    ];
}
