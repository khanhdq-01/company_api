<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service';

    protected $fillable = [
        'title', 'long_description', 'full_description', 'image', 'status', 'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
