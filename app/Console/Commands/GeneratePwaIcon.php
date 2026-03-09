<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class GeneratePwaIcon extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:generate-pwa-icon';

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
      $shop = Store::first();

      $iconDir = public_path('icon');

      if (!File::isDirectory($iconDir)) {
         File::makeDirectory($iconDir, 0775, true, true);
      }

      if ($shop->icon_path) {
         $rawFile = Image::read($shop->icon_path);
      } else {
         $rawFile = Image::read($shop->logo_path);
      }

      $sizes = [384, 256, 192, 180, 167, 152, 144, 128, 120, 96, 32, 16];

      foreach ($sizes as $numb) {
         $filename = "icon/icon-{$numb}x{$numb}.png";
         $iconPath = public_path($filename);
         if (File::exists($iconPath)) {
            File::delete($iconPath);
         }
         $rawFile->resizeDown($numb, $numb)->toPng()->save($filename);
      }
   }
}
