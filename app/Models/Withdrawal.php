<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use HasFactory;

    protected $guarded = [];

    const Pending = 'Pending';
    const Completed = 'Completed';
    const Cancelled = 'Cancelled';

    public $appends = [
        'can_process',
        'can_abort'
    ];

    public function evidence()
    {
        return $this->morphOne(Asset::class, 'assetable');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getCanProcessAttribute()
    {
        return $this->status == self::Pending;
    }
    public function getCanAbortAttribute()
    {
        return $this->status == self::Pending;
    }

    public static function pushRefCode($model)
    {
        $model->ref_code = 'WD' . now()->format('ym') . str_pad($model->id, 5, '0', STR_PAD_LEFT) . Str::upper(Str::random(3));
        $model->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $maxId = static::max('id');
            $model->ref_code = 'WD' . now()->format('ym') .str_pad($maxId + 1, 5, '0', STR_PAD_LEFT) . Str::upper(Str::random(3));
        });
    }

    public function mutasiSaldo()
    {
        return $this->morphMany(MutasiSaldo::class, 'entitiable');
    }
    public function mutasiSaldoFee()
    {
        return $this->morphOne(MutasiSaldo::class, 'entitiable')->where('is_fee', 1);
    }
}
