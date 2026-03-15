<?php

namespace App\Services\Message;

use Exception;
use App\Models\Wagateway;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
   protected $config;

   public function send($recipient, $message)
   {

      try {

         if (!$this->config) {
            $this->config = Wagateway::with('headerParams', 'bodyParams')->where('is_active', 1)->first();
         }
         if (!$this->config) {
            throw new Exception("The whatsapp config not configured");
         }
         if (!$recipient) {
            throw new Exception("Recipient not defined");
         }

         $endpoint = $this->config->endpoint;

         $pattern = [
            'apikey' => $this->config->apikey,
            'message' => $message,
            'phone' => formatPhoneWithPrefix($recipient)
         ];

         $headers = $this->render_params($this->config->headerParams, $pattern);
         $body = $this->render_params($this->config->bodyParams, $pattern);

         $instance = Http::withHeaders($headers);
         if ($this->config->content_type == 'form') {
            $instance->asForm();
         }
         if ($this->config->default_auth == 'Bearer') {
            $instance->withToken($this->config->apikey);
         }
         if ($this->config->default_auth == 'Basic') {
            $instance->withBasicAuth($this->config->apikey, '');
         }
         $response = $instance->post($endpoint, $body);

         // Log::debug($response);
         if ($response->failed()) {
            $response->throw();
         } else {
            $data = $response->json();

            if (isset($data['success']) && $data['success'] == false) {
               throw new Exception($data['message']);
            }
            return true;
         }
      } catch (\Exception $e) {
         throw $e;
      }
   }

   public function testing($id)
   {
      $shop = getShop();
      $recipient = $shop->phone;
      $textMessage = 'Testing Whatsapp gateway from ' . $shop->name;

      $this->config = Wagateway::with('headerParams', 'bodyParams')->where('id', $id)->first();

      return $this->send($recipient, $textMessage);
   }

   public function render_params($params, $pattern)
   {
      $m = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));

      $data = [];

      foreach ($params as $param) {
         if (str_contains($param->param_key, '|')) {
            $arr = explode('|', $param->param_key);
            $data[$arr[0]][$arr[1]] = $m->render($param->param_value, $pattern);
         } else if (str_contains($param->param_key, '/')) {
            $arr = explode('/', $param->param_key);
            $data[$arr[0]][$arr[1]] = $m->render($param->param_value, $pattern);
         } else {
            $data[$param->param_key] = $m->render($param->param_value, $pattern);
         }
      }

      return $data;
   }
}
