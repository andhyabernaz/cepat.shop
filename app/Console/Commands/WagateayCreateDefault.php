<?php

namespace App\Console\Commands;

use App\Models\Wagateway;
use App\Models\WagatewayParam;
use Illuminate\Console\Command;

class WagateayCreateDefault extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:wagateay-create-default';

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
      $defaultServices = [
         [
            'provider' => 'Fonnte',
            'endpoint' => 'https://api.fonnte.com/send',
            'apikey' => NULL,
            'params' => [
               [
                  'param_type' => Wagateway::PARAM_HEADER,
                  'param_key' => 'Authorization',
                  'param_value' => '{{ apikey }}'
               ],
               [
                  'param_type' => Wagateway::PARAM_BODY,
                  'param_key' => 'target',
                  'param_value' => '{{ phone }}'
               ],
               [
                  'param_type' => Wagateway::PARAM_BODY,
                  'param_key' => 'message',
                  'param_value' => '{{ message }}'
               ],
            ]

         ],
         [
            'provider' => 'Starsender',
            'endpoint' => 'https://starsender.online/api/sendText',
            'apikey' => NULL,
            'params' => [
               [
                  'param_type' => 'HEADER',
                  'param_key' => 'apikey',
                  'param_value' => '{{ apikey }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'tujuan',
                  'param_value' => '{{ phone }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'message',
                  'param_value' => '{{ message }}'
               ]
            ]
         ],
         [
            'provider' => 'Saungwa',
            'endpoint' => 'https://app.saungwa.com/api/create-message',
            'apikey' => NULL,
            'params' => [
               [
                  'param_type' => 'BODY',
                  'param_key' => 'authkey',
                  'param_value' => '{{ apikey }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'appkey',
                  'param_value' => null
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'to',
                  'param_value' => '{{ phone }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'message',
                  'param_value' => '{{ message }}'
               ]
            ]
         ],
         [
            'provider' => 'Ruangwa',
            'endpoint' => 'https://app.ruangwa.id/api/send_message',
            'apikey' => NULL,
            'params' => [
               [
                  'param_type' => 'BODY',
                  'param_key' => 'token',
                  'param_value' => '{{ apikey }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'number',
                  'param_value' => '{{ phone }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'message',
                  'param_value' => '{{ message }}'
               ]
            ]
         ],
         [
            'provider' => 'Onesender',
            'endpoint' => 'http://localhost:3000/api/v1/messages',
            'default_auth' => 'Bearer',
            'apikey' => NULL,
            'params' => [
               [
                  'param_type' => 'BODY',
                  'param_key' => 'recipient_type',
                  'param_value' => 'individual'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'type',
                  'param_value' => 'text'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'to',
                  'param_value' => '{{ phone }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'text|body',
                  'param_value' => '{{ message }}'
               ],
            ]
         ],
         [
            'provider' => 'Wapi',
            'endpoint' => 'http://localhost:3000/api/sendMessage',
            'apikey' => NULL,
            'params' => [
               [
                  'param_type' => 'BODY',
                  'param_key' => 'apiKey',
                  'param_value' => '{{ apikey }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'phone',
                  'param_value' => '{{ phone }}'
               ],
               [
                  'param_type' => 'BODY',
                  'param_key' => 'message',
                  'param_value' => '{{ message }}'
               ],
            ]
         ],
      ];

      foreach ($defaultServices as $service) {
         $service['is_default'] = 1;
         $wagateway = Wagateway::where('provider', $service['provider'])->where('is_default', 1)->first();

         if (!$wagateway) {
            $wagateway = Wagateway::create($service);
         }
         foreach ($service['params'] as $param) {
            if (WagatewayParam::where('param_key', $param['param_key'])->where('wagateway_id', $wagateway->id)->count() == 0) {
               $wagateway->params()->create($param);
            };
         }
      }
   }
}
