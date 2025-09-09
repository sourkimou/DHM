<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'client_hostname',
        'name',
        'model_number',
        'serial_number',
        'service_tag',
        'version',
        'manufacturer',
        'status',
    ];
}
