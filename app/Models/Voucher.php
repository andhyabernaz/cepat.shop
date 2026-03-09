<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
   use HasFactory;

   protected $fillable = [
      'name',
      'voucher_code',
      'start_date',
      'end_date',
      'discount_type',
      'discount_amount',
      'max_discount_amount',
      'min_transaction',
      'is_type_shipping',
      'usage_quota',
   ];

   protected $casts = [
      'is_type_shipping' => 'boolean'
   ];

   public $appends = ['detail_label', 'end_date_label', 'type_label'];

   public function scopeActive($query)
   {
      $now = new \DateTime();
      return $query->where('start_date', '<=', $now)->where('end_date', '>', $now);
   }
   public function scopeRunning($query)
   {
      $now = new \DateTime();
      return $query->where('start_date', '<=', $now)->where('end_date', '>', $now);
   }
   public function scopelater($query)
   {
      $now = new \DateTime();
      return $query->where('start_date', '>', $now)->where('end_date', '>', $now);
   }
   public function scopeExpired($query)
   {
      $now = new \DateTime();
      return $query->where('end_date', '<', $now);
   }
   public function orders()
   {
      return $this->belongsToMany(Order::class, 'order_vouchers', 'voucher_id', 'order_id');
   }
   public function getTypeLabelAttribute()
   {
      $voucherType = $this->is_type_shipping ? 'Gratis Ongkir hingga' : 'Voucher Diskon ';

      if ($this->discount_type == 'percent') {
         $label_disc = $voucherType . $this->discount_amount . '%';
      } else {
         $label_disc =  $voucherType . ' Rp' . shortNumber($this->discount_amount);
      }
      return $label_disc;
   }
   public function getDetailLabelAttribute()
   {
      $min_trx = $this->min_transaction > 0 ? shortNumber($this->min_transaction) : $this->min_transaction;
      $voucherType = $this->is_type_shipping ? 'Gratis Ongkir hingga' : 'Voucher Diskon ';

      if ($this->discount_type == 'percent') {
         $label_disc = $voucherType . $this->discount_amount . '%';
      } else {
         $label_disc = $voucherType . ' Rp' . shortNumber($this->discount_amount);
      }

      $mas_disc = $this->max_discount_amount > 0 ? ' hingga ' . shortNumber($this->max_discount_amount) : '';
      $min_trx_label = ' min belanja Rp' . $min_trx;

      return $label_disc . $mas_disc . $min_trx_label;
   }
   public function getEndDateLabelAttribute()
   {
      $exp = new Carbon($this->end_date);
      $now = Carbon::now();

      $difference = 'Expired';

      if ($exp > $now) {

         $difference = ($exp->diffInHours($now) < 24)
            ? 'Berakhir dalam ' . $exp->diffInHours($now) . ' jam'
            :  'Berakhir dalam ' . $exp->diffInDays($now) . ' hari';
      }

      return $difference;
   }
}
