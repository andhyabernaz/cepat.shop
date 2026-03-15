<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
   use HasFactory;

   protected $guarded = [];

   public $timestamps = false;

   public $appends = ['bank_detail'];

   public function getBankDetailAttribute()
   {
      return $this->bank_name . ' A/N ' . $this->account_name;
   }
}
