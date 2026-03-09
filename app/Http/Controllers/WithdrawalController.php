<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Withdrawal;
use App\Models\MutasiSaldo;
use App\Models\SaldoConfig;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationTemplate;
use App\Services\Media\MediaService;
use App\Jobs\DispatchNotificationJob;

class WithdrawalController extends Controller
{

   public function __construct(protected MediaService $mediaService) {}
   public function index(Request $request)
   {
      $data = Withdrawal::latest()->with('evidence', 'user')
         ->when($request->status, function ($query) use ($request) {
            $query->where('status', $request->status);
         })
         ->paginate($request->per_page ?? 4);

      return ApiResponse::success($data);
   }
   
   public function byUser(Request $request)
   {
      $data = Withdrawal::latest()->with('evidence', 'user')
         ->when($request->status, function ($query) use ($request) {
            $query->where('status', $request->status);
         })
         ->where('user_id', $request->user()->id)
         ->paginate($request->per_page ?? 4);

      return ApiResponse::success($data);
   }

   public function process(Request $request, $id)
   {
      $request->validate([
         'image' => 'required'
      ]);

      DB::beginTransaction();

      try {

         $withdrawal = Withdrawal::findOrFail($id);

         $data = $this->mediaService->storeFile($request->file('image'));

         $withdrawal->status = Withdrawal::Completed;
         $withdrawal->save();

         $withdrawal->evidence()->create([
            'filename' => $data['filename'],
            'filepath' => $data['filepath'],
            'disk' => $data['disk'],
         ]);

         DB::commit();

         $withdrawal->fresh();

         DispatchNotificationJob::dispatch(NotificationTemplate::WITHDRAW_PROCESSED, $withdrawal);

         return ApiResponse::success($withdrawal->load('evidence', 'user'));
      } catch (\Throwable $th) {
         DB::rollback();
         return ApiResponse::failed($th);
      }
   }
   public function abort(Request $request, $id)
   {
      $request->validate([
         'reason' => 'required'
      ]);

      DB::beginTransaction();

      try {

         $withdrawal = Withdrawal::findOrFail($id);
         $mutasiSaldos = $withdrawal->mutasiSaldo;

         $customer = $withdrawal->user;

         $withdrawal->status = Withdrawal::Cancelled;
         $withdrawal->reason = $request->reason;
         $withdrawal->save();

         $amount = (int) $mutasiSaldos->sum('amount');
         $desc = 'Pengembalian Dana Withdrawal';

         if ($mutasiSaldos->count() > 0) {
            $desc .= ' [Penarikan + Biaya]';
         }

         $desc .= ' Ref #' . $withdrawal->ref_code;

         $note = 'Withdrawal Gagal : ' . $request->reason;

         $saldoPayload = [
            'user_id' => $customer->id,
            'type' => MutasiSaldo::TYPE_IN,
            'category' => MutasiSaldo::CATEGORY_AFFILIATE,
            'amount' => $amount,
            'description' => $desc,
            'note' => $note,
         ];

         $withdrawal->mutasiSaldo()->create($saldoPayload);


         DB::commit();

         DispatchNotificationJob::dispatch(NotificationTemplate::WITHDRAW_ABORTED, $withdrawal);

         return ApiResponse::success($withdrawal->load('evidence', 'user'));
      } catch (\Exception $e) {
         DB::rollBack();
         return ApiResponse::failed($e);
      }
   }
   public function store(Request $request)
   {
      $request->validate([
         'amount' => 'required',
         'target_number' => 'required',
         'target_account' => 'required',
         'target_vendor' => 'required',
      ]);

      DB::beginTransaction();

      try {

         $user = $request->user();

         $config = SaldoConfig::first();

         if (!$user) {
            throw new Exception('User tidak valid');
         }

         $amount = (int) str_replace('.', '', $request->amount);
         $withdrawFee = intval($config->withdraw_fee);

         if ($withdrawFee > 0) {
            if ($user->affiliate_saldo < ($amount + $withdrawFee)) {
               throw new Exception('Saldo tidak cukup');
            }
         }

         $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'target_account' => $request->target_account,
            'target_number' => $request->target_number,
            'target_vendor' => $request->target_vendor,
            'note' => $request->note ?? NULL
         ]);

         $desc = 'Penarikan Saldo ' . money_format_idr($amount) . ' Ref #'  . $withdrawal->ref_code;

         $saldoPayload = [
            'user_id' => $user->id,
            'type' => MutasiSaldo::TYPE_OUT,
            'category' => MutasiSaldo::CATEGORY_AFFILIATE,
            'amount' => $amount,
            'description' => $desc,
         ];

         $withdrawal->mutasiSaldo()->create($saldoPayload);

         if ($withdrawFee > 0) {

            $saldoPayload = [
               'user_id' => $user->id,
               'amount' => $withdrawFee,
               'type' => MutasiSaldo::TYPE_OUT,
               'category' => MutasiSaldo::CATEGORY_AFFILIATE,
               'description' => 'Biaya Penarikan Saldo Ref #' . $withdrawal->ref_code,
               'is_fee' => 1,
            ];

            $withdrawal->mutasiSaldo()->create($saldoPayload);
         }

         DB::commit();

         $event = NotificationTemplate::WITHDRAW_CREATED;

         DispatchNotificationJob::dispatch($event, $withdrawal);

         return ApiResponse::withEvent($event)->success();
      } catch (\Exception $e) {
         DB::rollback();
         Log::error($e);

         return ApiResponse::failed($e->getMessage());
      }
   }
}
