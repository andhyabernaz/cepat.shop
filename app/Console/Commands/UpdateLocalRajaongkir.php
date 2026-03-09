<?php

namespace App\Console\Commands;

use App\Models\Subdistrict;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateLocalRajaongkir extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'rajaongkir:subdistrict-local';

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
      DB::table('subdistricts')->truncate();
      DB::connection('rajaongkir')->table('subdistricts')->orderBy('id')->chunk(500, function ($rows) {
         foreach ($rows as $row) {
            Subdistrict::create([
               'subdistrict_id' => $row->subdistrict_id,
               'subdistrict_name' => $row->subdistrict_name,
               'city_id' => $row->city_id,
               'city' => $row->city,
               'province_id' => $row->province_id,
               'province' => $row->province,
            ]);
         }
      });
   }
}
