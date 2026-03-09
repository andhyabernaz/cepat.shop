<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Services\RajaongkirService;
use Illuminate\Console\Command;
use Silehage\Rajaongkir\Facades\Rajaongkir;

class UpdateRajaongkirCity extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'rajaongkir:update_city';

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
      $city = (new RajaongkirService)->cityAll();

      $data = json_decode($city, true);

      City::insert($data['data']);
   }
}
