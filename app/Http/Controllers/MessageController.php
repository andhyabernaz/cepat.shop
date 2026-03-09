<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Services\Message\MessageService;

class MessageController extends Controller
{
   public function index(Request $request)
   {
      $data = Message::when($request->status, function ($q) use ($request) {
         $q->where('status', $request->status);
      })
         ->when($request->is_admin, function ($q) {
            $q->whereNull('user_id');
         })
         ->latest()
         ->paginate($request->per_page ?? 10)
         ->withQueryString();

      return ApiResponse::success($data);
   }

   public function send(MessageService $messageService, $id)
   {
      try {
         $message = Message::findOrFail($id);
         $messageService->sendMessage($message);
         $message->pushComplete();
         return ApiResponse::withMessage('Message sent successfully')->success();
      } catch (\Throwable $th) {
         $message->pushFailed($th->getMessage());
         return ApiResponse::failed($th->getMessage());
      }
   }
}
