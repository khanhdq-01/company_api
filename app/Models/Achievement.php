<?php

// app/Models/Achievement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'date',
        'summary',
        'description',
        'image_path',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}