<?php

namespace App\Models;

use App\Enums\ProductTypeEnum;
use App\Jobs\DispatchEventJob;
use App\Jobs\DispatchNotificationJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const PENDING = 'PENDING';
    public const TO_PROCESS = 'TO_PROCESS';
    public const COMPLETE = 'COMPLETE';
    public const CANCELED = 'CANCELED';

    public const PAYMENT_GATEWAY = 'PAYMENT_GATEWAY';
    public const PAYMEMT_DIRECT_TRANSFER = 'DIRECT_TRANSFER';
    public const PAYMEMT_CASH = 'CASH';
    public const PAYMEMT_COD = 'COD';
    public const PAYMEMT_SALDO_BALANCE = 'SALDO_BALANCE';

    public $appends = [
        'admin_status',
        'customer_status',
        'created',
        'billing_total',
        'can_review',
        'can_completed',
        'can_cancel_order',
        'can_confirm_payment',
        'can_delete_order',
    ];

    protected $with = ['transaction'];

    public function getBillingTotalAttribute()
    {
        return $this->order_total + $this->payment_fee;
    }

    public function getCanCancelOrderAttribute()
    {
        return in_array($this->order_status, [self::PENDING, self::TO_PROCESS]);
    }

    public function getCanDeleteOrderAttribute()
    {
        return $this->order_status === self::CANCELED;
    }

    public function is_deposit_type()
    {
        return $this->product_type == ProductTypeEnum::Deposit->value;
    }

    public function is_digital_type()
    {
        return ProductTypeEnum::isDigital($this->product_type);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function item()
    {
        return $this->hasOne(OrderItem::class);
    }

    public function getCreatedAttribute()
    {
        return $this->created_at->format('d/m/Y ~ H:i');
    }

    public function getCanReviewAttribute()
    {
        if ($this->is_deposit_type()) {
            return false;
        }
        $userId = getCurrentSanctumUser('id');

        return ! $this->is_reviewed && $this->user_id == $userId && $this->order_status == self::COMPLETE;
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function getAdminStatusAttribute()
    {
        $reason = $this->cancellation_reason ? ': '.$this->cancellation_reason : '';
        switch ($this->order_status) {
            case self::CANCELED:
                return [
                    'label' => 'Dibatalkan',
                    'title' => 'Pesanan Dibatalkan',
                    'desc' => 'Pesanan dibatalkan '.$reason,
                    'icon' => 'production_quantity_limits',
                ];
            case self::TO_PROCESS:
                return [
                    'label' => 'Perlu Diproses',
                    'title' => 'Pesanan perlu diproses',
                    'desc' => 'Pesanan digital sedang diproses',
                    'icon' => 'av_timer',
                ];
            case self::COMPLETE:
                return [
                    'label' => 'Selesai',
                    'title' => 'Pesanan Selesai',
                    'desc' => 'Pesanan selesai',
                    'icon' => 'receipt_long',
                ];
            default:
                $exp = Carbon::parse($this->expired_at)->translatedFormat('l, d M Y H:i:s');

                return [
                    'label' => 'Pending',
                    'title' => 'Menunggu Pembayaran',
                    'desc' => 'Menuggu pembayaran dari pembeli, batas pembayaran '.$exp,
                    'icon' => 'payments',
                ];
        }
    }

    public function getCustomerStatusAttribute()
    {
        return $this->admin_status;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $prefix = $model->product_type == ProductTypeEnum::Deposit->value ? 'DPO' : 'INV';
            $model->order_ref = $prefix.Carbon::now()->format('ym').str_pad($model->id, 6, '0', STR_PAD_LEFT).Str::upper(Str::random(3));
            $model->save();
            $model->pushHistory('Pesanan dibuat');
            self::clearCache();
        });

        static::updated(function () {
            self::clearCache();
        });
    }

    public function pushHistory($desc)
    {
        $this->histories()->create([
            'description' => $desc,
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ]);
    }

    public function dispatchEventMessage($event, $sync = false)
    {
        $order = $this;
        $order->fresh();
        DispatchNotificationJob::dispatch($event, $order);

        if ($sync && ! Cache::has('is_cron')) {
            DispatchEventJob::dispatch($event);
        }
    }

    public function getCanConfirmPaymentAttribute()
    {
        $transaction = $this->transaction;
        if (! $transaction) {
            return false;
        }

        return in_array($this->order_status, [self::PENDING, self::TO_PROCESS]) && in_array($transaction->status, ['UNPAID']);
    }

    public function getCanCompletedAttribute()
    {
        return $this->order_status === self::TO_PROCESS;
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class)->orderByDesc('id');
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'order_vouchers', 'order_id', 'voucher_id');
    }

    public static function statusOptions()
    {
        return [
            ['label' => 'Semua', 'value' => 'ALL'],
            ['label' => 'Pending', 'value' => self::PENDING],
            ['label' => 'Perlu Diproses', 'value' => self::TO_PROCESS],
            ['label' => 'Selesai', 'value' => self::COMPLETE],
            ['label' => 'Batal', 'value' => self::CANCELED],
        ];
    }

    public static function eventOptions()
    {
        return [
            ['label' => 'PENDING', 'value' => self::PENDING],
            ['label' => 'TO_PROCESS', 'value' => self::TO_PROCESS],
            ['label' => 'COMPLETE', 'value' => self::COMPLETE],
            ['label' => 'CANCELED', 'value' => self::CANCELED],
        ];
    }

    public static function clearCache()
    {
        Cache::forget('latest_orders');
        Cache::forget('order_reports');
        Cache::forget('transaction_reports');
    }

    public function flush_cache()
    {
        self::clearCache();
    }
}
