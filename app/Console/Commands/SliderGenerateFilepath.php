<?php

namespace App\Console\Commands;

use App\Models\Slider;
use Illuminate\Console\Command;

class SliderGenerateFilepath extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:slider-generate-filepath';

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
      $sliders = Slider::all();

      foreach ($sliders as $slider) {
         $slider->update([
            'filepath' => 'upload/images/' . $slider->filename
         ]);
      }
   }
}
