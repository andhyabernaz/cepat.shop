<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class License extends Model
{
    use HasFactory;

     protected $guarded = [];

    public $appends = ['is_active', 'active_label'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function getActiveLabelAttribute()
    {
        $label = 'Unlimited';
        if ($this->expired_at) {
            if ($this->expired_at > Carbon::now()) {
                return Carbon::parse($this->expired_at)->diffForHumans();
            } else {
                $label = 'Expired';
            }
        }

        return $label;
    }

    public function scopeActive($query)
    {
        return $query->whereNull('expired_at')
            ->orWhere('expired_at', '>=', now());
    }

    public function getIsActiveAttribute()
    {
        if ($this->expired_at && $this->expired_at < Carbon::now()) {
            return false;
        }

        return true;
    }
}
