<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'subject',
      'message',
      'send_at',
      'is_read',
      'log',
   ];
}
