<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'ttl',
        'description',
        'is_active',
        'is_auto_active',
        'welcome_message',
        'suspend_message'
    ];

    public $timestamps = false;

    protected $casts = [
        'is_active' => 'boolean',
        'is_auto_active' => 'boolean',
    ];
}
