<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ImportAddressTableSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      Artisan::call('app:import-table-address');
   }
}
