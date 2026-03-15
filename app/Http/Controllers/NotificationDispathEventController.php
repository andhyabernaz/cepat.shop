<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Jobs\DispatchEventJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NotificationDispathEventController extends Controller
{
   /**
    * Handle the incoming request.
    */
   public function __invoke(Request $request)
   {
      $request->validate([
         'event' => 'required'
      ]);

      if (! is_cron_running()) {
         DispatchEventJob::dispatch($request->event);
      }

      return ApiResponse::success();
   }
}
