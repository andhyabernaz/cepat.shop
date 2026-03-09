<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoConfig extends Model
{
    use HasFactory;

     protected $fillable = [
        'min_deposit_amount',
        'min_withdraw_amount',
        'max_withdraw_amount',
        'deposit_description',
        'withdraw_description',
        'withdraw_channels',
        'withdraw_fee',
    ];

    public $timestamps = false;

    protected $casts = [
        'withdraw_channels' => 'array'
    ];
}
