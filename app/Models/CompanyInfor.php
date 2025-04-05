<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfor extends Model
{
    protected $table = 'company_infors';

    protected $fillable = [
        'company_name',
        'address',
        'director_name',
        'founded_date',
        'business_scope',
        'capital',
        'group_parent',
        'group_subsidiaries',
        'employee_count',
    ];

    protected $casts = [
        'founded_date' => 'date',
        'group_subsidiaries' => 'array', // Tự động decode JSON thành array
    ];
}
