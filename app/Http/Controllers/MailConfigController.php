<?php

namespace App\Http\Controllers;

use App\Models\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MailConfigController extends Controller
{
   public function show()
   {
      return response()->json([
         'success' => true,
         'data' => MailConfig::first(),
         'mail' => config('mail')
      ]);
   }

   public function update(Request $request)
   {
      $request->validate([
         'is_active' => 'required',
      ]);

      $mailConfig = MailConfig::first();

      $mailConfig->update($request->all());

      Artisan::call('optimize:clear');

      return response()->json([
         'success' => true,
         'data' => $mailConfig->fresh(),
         'mail' => config('mail')
      ]);
   }
}
