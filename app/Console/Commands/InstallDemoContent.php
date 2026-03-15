<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallDemoContent extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:install-demo-content';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Command description';

   /**
    * Execute the console command.
    */
   public function handle()
   {
      try {

         Log::debug('run app:install-demo-conten');

         if (!File::exists(database_path('demo/assets')) || !File::exists(database_path('demo/content.sql'))) {
            throw new Exception('demo not found');
         }

         $this->line('Copying assets please wait...');
         File::deleteDirectory(public_path('upload/images'));
         File::copyDirectory(database_path('demo/assets'), public_path('upload/images'));

         $this->line('Import Content please wait...');
         $sql = database_path('demo/content.sql');
         DB::unprepared(file_get_contents($sql));

         Artisan::call('optimize:clear');
      } catch (\Throwable $th) {

         $this->info($th->getMessage());
         Log::info($th->getMessage());
      }
   }
}
