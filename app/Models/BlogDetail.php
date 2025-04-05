<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogDetail extends Model
{
    protected $fillable = ['blog_id'];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}
