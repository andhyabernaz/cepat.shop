<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Helpers\ApiResponse;
use App\Http\Requests\NotifTemplateRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\NotificationTemplate;

class NotificationTemplateController extends Controller
{
   public function index()
   {
      $data =  [
         'templates' => NotificationTemplate::all(),
         'params' => NotificationTemplate::getParams(),
         'via' => Message::getViaOptions(),
      ];
      return ApiResponse::success($data);
   }
   public function update(NotifTemplateRequest $request, $id)
   {
      $cfg = NotificationTemplate::find($id);
      $cfg->update($request->all());

      return ApiResponse::success();
   }
   public function store(NotifTemplateRequest $request)
   {
      $validated = $request->validated();
      NotificationTemplate::create($validated);

      return ApiResponse::success();
   }
   public function destroy($id)
   {
      NotificationTemplate::find($id)->delete();
      return ApiResponse::success();
   }
   public function getOrderStatusOptions()
   {
      $data = Order::eventOptions();

      return ApiResponse::success($data);
   }
   public function getOrderEventOptions()
   {
      $data = Order::eventOptions();

      return ApiResponse::success($data);
   }
   public function sort(Request $request)
   {
      $numb = 1;
      foreach ($request->templates as $template) {
         NotificationTemplate::where('id', $template['id'])->update(['sort' => $numb]);
         $numb++;
      }

      return ApiResponse::success();
   }
   public function getOrderOptions()
   {
      $data = [
         'order_status_options' => Order::statusOptions(),
         'order_event_options' => NotificationTemplate::eventOptions(),
      ];

      return ApiResponse::success($data);
   }
}
