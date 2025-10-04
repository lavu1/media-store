<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequests extends Model
{
    protected $table = 'service_requests';

    protected $fillable = [
        'type',
        'days',
        'name',
        'email',
        'phone',
        'education_background',
        'work_experience',
        'skills',
        'status',
        'cv_file_path',
        'additional_notes'
    ];
}
