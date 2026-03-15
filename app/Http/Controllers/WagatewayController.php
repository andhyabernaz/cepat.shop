<?php

namespace App\Http\Controllers;

use App\Models\Wagateway;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Services\Message\WhatsappService;

class WagatewayController extends Controller
{
   public function __construct(protected WhatsappService $whatsappService)
   {
   }
   public function index()
   {
      $data = Wagateway::with('params')->get();

      return ApiResponse::success($data);
   }
   public function store(Request $request)
   {
      $request->validate([
         'endpoint' => 'required',
         'provider' => 'required',
         'params' => 'required|array'
      ]);

      $gateway = Wagateway::create([
         'endpoint' => $request->endpoint,
         'provider' => $request->provider,
         'default_auth' => $request->default_auth ?? null,
         'apikey' => $request->apikey ?? NULL,
      ]);

      foreach ($request->params as $param) {
         $gateway->params()->create($param);
      }
   }
   public function setAsDefault($id)
   {
      $data = Wagateway::find($id);
      if ($data->is_active) {
         $data->update(['is_active' => 0]);
      } else {
         Wagateway::where('id', '!=', $id)->update(['is_active' => 0]);
         Wagateway::where('id', $id)->update(['is_active' => 1]);
      }
      return ApiResponse::success();
   }
   public function update(Request $request, $id)
   {
      $request->validate([
         'endpoint' => 'required',
         'provider' => 'required',
         'params' => 'required|array'
      ]);

      $gateway = Wagateway::findOrFail($id);

      $gateway->update([
         'endpoint' => $request->endpoint,
         'provider' => $request->provider,
         'default_auth' => $request->default_auth ?? null,
         'apikey' => $request->apikey ?? NULL,
         'content_type' => $request->content_type,
      ]);

      $gateway->params()->delete();

      foreach ($request->params as $param) {
         $gateway->params()->create($param);
      }

      return ApiResponse::success();
   }
   public function destroy($id)
   {
      $gateway = Wagateway::findOrFail($id);

      $gateway->delete();

      return ApiResponse::success();
   }
   public function testing($id)
   {

      try {
         $this->whatsappService->testing($id);

         return ApiResponse::success('Testing success');
      } catch (\Exception $th) {
         return ApiResponse::failed($th);
      }
   }
}
