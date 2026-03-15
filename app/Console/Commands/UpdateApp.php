<?php

namespace App\Console\Commands;

use App\Models\PaymentConfig;
use App\Utilities\Installer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class UpdateApp extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:update';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Update Sites';

   /**
    * Create a new command instance.
    *
    * @return void
    */
   public function __construct()
   {
      parent::__construct();
   }

   /**
    * Execute the console command.
    *
    * @return int
    */
   public function handle()
   {

      try {

         Artisan::call('migrate', ['--force' => true]);

         Artisan::call('asset:update-path');

         Artisan::call('app:generate-post-asset-cmd');

         Artisan::call('order:change-status');

         Artisan::call('app:generate-manifest');

         PaymentConfig::createDefault();

         Installer::finalTouches();

         Artisan::call('optimize:clear');
      } catch (\Exception $e) {

         Log::info('error site update');
      }
   }
}
