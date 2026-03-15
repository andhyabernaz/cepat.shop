<?php

namespace App\Console\Commands;

use App\Models\DigitalDownload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DigitalDownloadAutoDeleteFile extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:auto-delete-files';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Command description';

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
      $files = DigitalDownload::onlyTrashed()->get();

      foreach ($files as $file) {
         if ($file->download_type == 'file') {
            Storage::disk($file->disk)->delete($file->filepath);
         }
         $file->forceDelete();
      }

      Artisan::call('app:delete-file-temp');
   }
}
