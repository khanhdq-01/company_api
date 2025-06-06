<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = ['name'];

    public function tabs()
    {
        return $this->hasMany(PricingTab::class);
    }
}