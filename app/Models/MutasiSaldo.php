<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MutasiSaldo extends Model
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'type',
      'status',
      'amount',
      'last_saldo',
      'description',
      'note',
      'category'
   ];

   const TYPE_IN = 'IN';
   const TYPE_OUT = 'OUT';

   const CATEGORY_AFFILIATE = 'Affiliate';
   const CATEGORY_DEFAULT = 'Default';

   public function user()
   {
      return $this->belongsTo(User::class);
   }

   public static function boot()
   {
      parent::boot();

      static::creating(function ($model) {

         try {

            $user = User::lockForUpdate()->find($model->user_id);

            $lastSaldo = 0;
            $currentSaldo = 0;

            if ($model->category == self::CATEGORY_AFFILIATE) {
               $lastSaldo = $user->affiliate_saldo;

               if ($model->type == self::TYPE_OUT) {
                  $currentSaldo =  $lastSaldo - $model->amount;
               }

               if ($model->type == self::TYPE_IN) {
                  $currentSaldo =  $lastSaldo + $model->amount;
               }
               $user->affiliate_saldo = $currentSaldo;
            } else {
               $lastSaldo = $user->saldo_balance;
               if ($model->type == self::TYPE_OUT) {
                  $currentSaldo =  $lastSaldo - $model->amount;
               }

               if ($model->type == self::TYPE_IN) {
                  $currentSaldo =  $lastSaldo + $model->amount;
               }

               $user->saldo_balance = $currentSaldo;
            }

            $user->save();
            $model->last_saldo = $currentSaldo;
         } catch (\Throwable $th) {
            throw $th;
         }
      });
   }
}
