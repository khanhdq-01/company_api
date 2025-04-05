<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberOther extends Model
{
    protected $table = 'member_others';
    protected $fillable = ['name', 'position','image_path'];
}