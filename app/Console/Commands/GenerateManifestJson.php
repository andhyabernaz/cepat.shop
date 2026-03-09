<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\Config;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class GenerateManifestJson extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:generate-manifest';

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
      $shop = Store::first();
      $config = Config::first();

      if (!$shop->icon_path || !$shop->logo_path) {
         return;
      }

      Artisan::call('app:generate-pwa-icon');

      $data = [
         'name' => $shop->app_name ?? $shop->name,
         'short_name' => $shop->app_name ?? $shop->name,
         'description' => $shop->slogan ?? '',
         "display" => "standalone",
         "start_url" => "/",
         "icons" => [
            ["src" => "/icon/icon-128x128.png", "sizes" => "128x128", "type" => "image/png"],
            ["src" => "/icon/icon-144x144.png", "sizes" => "144x144", "type" => "image/png"],
            ["src" => "/icon/icon-152x152.png", "sizes" => "152x152", "type" => "image/png"],
            ["src" => "/icon/icon-167x167.png", "sizes" => "167x167", "type" => "image/png"],
            ["src" => "/icon/icon-180x180.png", "sizes" => "180x180", "type" => "image/png"],
            ["src" => "/icon/icon-192x192.png", "sizes" => "192x192", "type" => "image/png"],
            ["src" => "/icon/icon-256x256.png", "sizes" => "256x256", "type" => "image/png"],
            ["src" => "/icon/icon-384x384.png", "sizes" => "384x384", "type" => "image/png"],
         ],
         "lang" => "id",
         "background_color" => "#ffffff",
         "theme_color" => $config->theme_color,
         "dir" => "ltr",
         "orientation" => "portrait",
         "prefer_related_applications" => false
      ];

      $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

      $filepath = public_path("manifest.json");

      if (File::exists($filepath)) {
         File::delete($filepath);
      }

      File::put($filepath, $json);
   }
}
