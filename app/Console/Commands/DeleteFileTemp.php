<?php

namespace App\Console\Commands;

use App\Models\UploadTemp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteFileTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-file-temp';

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
        $temps = UploadTemp::where('created_at', '<', now()->subHour())->get();

      foreach ($temps as $file) {
         Storage::disk($file->disk)->delete($file->filepath);
         $file->delete();
      }
    }
}
