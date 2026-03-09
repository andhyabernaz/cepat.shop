<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class InstallApp extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:install
                           {--shop_name=Cepatshop}
                           {--shop_phone=083842587851}
                           {--admin_name=Admin}
                           {--admin_email=admin@example.com}
                           {--admin_password=admin123}
                           {--with_demo=0}
                           ';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Install Aplikasi';

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
      $bar = $this->output->createProgressBar(3);

      $this->line('Migrate and Seeding Database please wait...');

      Artisan::call('migrate:fresh', ['--force' => true]);

      $bar->advance();
      $this->newLine();
      Artisan::call('db:seed', ['--force' => true]);

      $bar->advance();
      $this->newLine();

      $shop_name = $this->option('shop_name');
      $shop_phone = $this->option('shop_phone');
      $admin_name = $this->option('admin_name');
      $admin_email = $this->option('admin_email');
      $admin_password = $this->option('admin_password');
      $with_demo = $this->option('with_demo');

      $shop = Store::firstOrNew();
      $shop->name = $shop_name;
      $shop->phone = $shop_phone;
      $shop->save();

      $admin = User::firstOrNew();
      $admin->name = $admin_name;
      $admin->email = $admin_email;
      $admin->password = Hash::make($admin_password);
      $admin->save();

      if ($with_demo == "1") {
         Artisan::call('app:install-demo-content');
      }

      Artisan::call('optimize:clear');

      $bar->finish();

      $this->newLine();
      $this->info('Berhasil menginstall aplikasi');
   }
}
