<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Services\Message\MessageService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchEventJob implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   public $event;
   /**
    * Create a new job instance.
    */
   public function __construct($event)
   {
      $this->event = $event;
   }

   /**
    * Execute the job.
    */
   public function handle(MessageService $messageService): void
   {
      $data = Message::where('event', $this->event)->where('status', Message::Pending)->get();

      foreach ($data as $message) {
         try {
            $messageService->sendMessage($message);
            $message->pushComplete();
         } catch (\Throwable $th) {
            $message->pushFailed($th->getMessage());
            continue;
         }
      }
   }
}
