<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\Message\MessageService;

class SendMessage extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:send-message';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Command description';

   /**
    * Execute the console command.
    */
   public function handle(MessageService $messageService)
   {
      Cache::put('is_cron', 1, now()->addMinutes(2));

      if (Cache::has('is_send_message')) {
         return 0;
      }
      $data = Message::where('status', Message::Pending)->get();

      Log::info('cron running');

      Cache::put('is_send_message', 1, now()->addHour());
      foreach ($data as $message) {
         try {
            $messageService->sendMessage($message);
            $message->pushComplete();
         } catch (\Throwable $th) {
            $message->pushFailed($th->getMessage());
            continue;
         }
      }
      Cache::forget('is_send_message');
   }
}
