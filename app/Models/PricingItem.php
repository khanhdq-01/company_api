<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingItem extends Model
{
    protected $fillable = ['pricing_tab_id', 'title', 'price', 'period'];

    public function pricingTab()
    {
        return $this->belongsTo(PricingTab::class);
    }

    public function features()
    {
        return $this->hasMany(PricingFeature::class);
    }
}