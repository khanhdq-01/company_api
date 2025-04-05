<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTab extends Model
{
    protected $fillable = ['pricing_plan_id', 'name'];

    public function pricingPlan()
    {
        return $this->belongsTo(PricingPlan::class);
    }

    public function items()
    {
        return $this->hasMany(PricingItem::class);
    }
}
