<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use App\Models\Message;
use App\Models\MutasiSaldo;
use Illuminate\Bus\Queueable;
use App\Models\NotificationTemplate;
use App\Models\Withdrawal;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Message\NotificationService;

class DispatchNotificationJob implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   public $event;
   public $model;

   /**
    * Create a new job instance.
    */
   public function __construct(string $event, $model)
   {
      $this->event = $event;
      $this->model = $model;
   }

   /**
    * Execute the job.
    */
   public function handle(NotificationService $notificationService): void
   {
      $templates = NotificationTemplate::where('event', $this->event)->get();

      foreach ($templates as $template) {

         $recipient = null;
         $body = $notificationService->renderMessage($this->model, $template->template);
         $subject = $template->subject;

         $is_order = false;
         $is_mutasi = false;

         $user = null;

         if ($this->model instanceof User) {
            $user = $this->model;
         } else if ($this->model instanceof Order) {
            $user = $this->model->user;
            $is_order = true;
         } else if ($this->model instanceof MutasiSaldo) {
            $user = $this->model->user;
            $is_order = false;
         } else if ($this->model instanceof Withdrawal) {
            $user = $this->model->user;
            $is_order = false;
         }

         if ($template->role == Message::RoleCustomer) {
            if ($template->via == Message::VIA_EMAIL) {
               $recipient = $user ? $user->email : null;
            }
            if ($template->via == Message::VIA_WHATSAPP) {
               if ($user) {
                  $recipient = $user->phone;
               } 
               if ($is_order) {
                  $recipient = $this->model->customer_whatsapp;
               } 
            }
         }
         if ($template->role == Message::RoleAdmin) {
            $shop = Store::first();

            if ($template->via == Message::VIA_EMAIL) {
               $recipient = $shop->email;
            }

            if ($template->via == Message::VIA_TELEGRAM) {
               $recipient = 'Admin Telegram';
            }

            if ($template->via == Message::VIA_WHATSAPP) {
               $recipient = $shop->phone;
            }
         }

         if (!$recipient) {
            continue;
         }

         Message::create([
            'user_id' => $user?->id,
            'via' => $template->via,
            'event' => $template->event,
            'recipient' => $recipient,
            'subject' => $subject,
            'body' => $body
         ]);
      }
   }
}
