<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Installed
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
    * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    */
   public function handle(Request $request, Closure $next)
   {
      if (config('installer.installed') == true) {
         return $next($request);
      }

      if ($request->wantsJson()) {

         return response()->json([
            'success' => false,
            'is_installed' => false,
            'message' => 'The application has not been installed'
         ], 503);
      }

      return redirect()->route('install');
   }
}
