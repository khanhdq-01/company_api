<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    use HasFactory;
    protected $fillable = [
        'position',
        'department',
        'quantity',
        'job_type',
        'start_date',
        'deadline',
        'status',
        'notes'
    ];
}
