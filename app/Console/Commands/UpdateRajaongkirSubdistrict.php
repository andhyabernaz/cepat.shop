<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Subdistrict;
use App\Services\RajaongkirService;
use Illuminate\Console\Command;
use Silehage\Rajaongkir\Facades\Rajaongkir;

class UpdateRajaongkirSubdistrict extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'rajaongkir:update_subdistrict';

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
      $cities = City::all();

      foreach ($cities as $city) {

         usleep(25000);

         $subdistricts = (new RajaongkirService)->subdistrict($city->city_id);

         $data = json_decode($subdistricts, true);

         Subdistrict::insert($data['data']);
      }
   }
}
