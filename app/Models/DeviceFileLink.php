<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceFileLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'file_url',
        'file_type',
    ];
}
