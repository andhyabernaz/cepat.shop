<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class GeneratePostAssetCmd extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:generate-post-asset-cmd';

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
      $posts = Post::select('id', 'image', 'title')
         ->whereNotNull('image')
         ->doesntHave('asset')->get();

      foreach ($posts as $post) {
         $post->asset()->create([
            'filename' => $post->image,
            'filepath' => 'upload/images/' . $post->image,
         ]);
      }
   }
}
