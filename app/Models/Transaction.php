<?php

namespace App\Models;

use App\Services\Channel\EventBuffer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
   use HasFactory;

   protected $guarded = [];

   public $appends = [
      'status_label',
      'payment_proof_url',
      'can_upload_payment_proof'
   ];

   const PAID = 'PAID';
   const REFUND = 'REFUND';
   const EXPIRED = 'EXPIRED';
   const UNPAID = 'UNPAID';
   const FAILED = 'FAILED';

   public function order()
   {
      return $this->belongsTo(Order::class);
   }
   public function getStatusLabelAttribute()
   {

      switch ($this->status) {
         case self::PAID:
            return 'Dibayar';
            break;
         case self::UNPAID:
            if($this->payment_proof) {
               return 'Menuggu Verifikasi';
            }
            return 'Belum Bayar';
            break;
         case self::EXPIRED:
            return 'Expired';
            break;
         case self::FAILED:
            return 'Gagal';
            break;

         default:
            return 'Belum Bayar';
            break;
      }
   }

   public function getPAymentProofUrlAttribute()
   {
      return $this->payment_proof ? url($this->payment_proof) : null;
   }

   public function getCanUploadPaymentProofAttribute()
   {
      if ($this->payment_type == Order::PAYMEMT_DIRECT_TRANSFER && !$this->payment_proof) {
         return true;
      }

      return false;
   }

   protected static function boot()
   {
      parent::boot();

      static::updated(function ($model) {
         $cacheKey = 'payments_updated_' . $model->id;
         Cache::put($cacheKey, ['event' => 'payment.updated', 'payload' => $model->status], now()->addMinutes(60));
      });
   }
}
