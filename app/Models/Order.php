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

    public const PROCESS = 'PROCESS';

    public const TOSHIP = 'TOSHIP';

    public const TO_PROCESS = 'TO_PROCESS';

    public const SHIPPING = 'SHIPPING';

    public const COMPLETE = 'COMPLETE';

    public const CANCELED = 'CANCELED';

    public const AWAITING_PICKUP = 'AWAITING_PICKUP';

    public const SHIPPING_COD = 'COD';

    public const SHIPPING_PICKUP = 'PICKUP';

    public const SHIPPING_COURIER = 'COURIER';

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
        'subtotal_shipping_cost',
        'can_check_waybill',
        'can_review',
        'can_completed',
        'can_input_resi',
        'can_cancel_order',
        'can_confirm_payment',
        'can_shipping',
        'can_delete_order',
    ];

    protected $with = ['transaction'];

    public function getBillingTotalAttribute()
    {
        return $this->order_total + $this->payment_fee;
    }

    public function getSubtotalShippingCostAttribute()
    {
        return $this->shipping_cost - $this->shipping_discount;
    }

    public function getCanCancelOrderAttribute()
    {
        return in_array($this->order_status, [self::PENDING, self::TOSHIP, self::AWAITING_PICKUP]);
    }

    public function getCanDeleteOrderAttribute()
    {
        return in_array($this->order_status, [self::CANCELED]);
    }

    public function getCanInputResiAttribute()
    {
        if ($this->shipping_courier_id == 'COD' || $this->shipping_courier_id == 'PICKUP') {
            return false;
        }

        if ($this->order_status == self::TOSHIP || $this->order_status == self::SHIPPING) {
            return true;
        }

        return false;
    }

    public function getCanShippingAttribute()
    {
        $transaction = $this->transaction;

        if ($this->order_status == self::PENDING && $transaction && $transaction->payment_type == self::PAYMEMT_COD) {
            return true;
        }

        if ($this->order_status == self::TOSHIP && ($this->shipping_courier_code || $this->shipping_courier_id == self::SHIPPING_COD)) {
            return true;
        }

        return false;
    }

    public function is_deposit_type()
    {
        return $this->product_type == ProductTypeEnum::Deposit->value;
    }

    public function is_digital_type()
    {
        return ProductTypeEnum::isDigital($this->product_type);
    }

    public function is_default_type()
    {
        return $this->product_type == ProductTypeEnum::Default->value;
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

    public function getCreatedReadableAttribute()
    {
        return $this->created_at->diffForHumans();
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

        if (! $this->is_reviewed && $this->user_id == $userId && $this->order_status == self::COMPLETE) {
            return true;
        }

        return false;
    }

    public function getCanCheckWaybillAttribute()
    {
        return $this->order_status != self::PENDING;
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
                break;
            case self::TOSHIP:
                return [
                    'label' => 'Perlu Dikirim',
                    'title' => 'Pesanan Perlu Dikirim',
                    'desc' => 'Segera kemas dan kirim pesanan ke pembeli',
                    'icon' => 'move_to_inbox',
                ];
                break;
            case self::SHIPPING:
                return [
                    'label' => 'Dikirim',
                    'title' => 'Pesanan Sedang Dikirim',
                    'desc' => 'Pesanan sedang dalam proses pengiriman',
                    'icon' => 'local_shipping',
                ];
                break;
            case self::TO_PROCESS:

                $transaction = $this->transaction;
                if ($transaction && $transaction->status == 'UNPAID' && $transaction->payment_proof) {
                    return [
                        'label' => 'Bukti pembayaran dikirim',
                        'title' => 'Menunggu Verifikasi Pembayaran',
                        'desc' => 'Segera cek pembayaran dari pelanggan, jika sudah valid silahkan tekan konfirmasi pembayaran',
                        'icon' => 'payments',
                    ];
                }

                return [
                    'label' => 'Perlu Diproses',
                    'title' => 'Pesanan perlu diproses',
                    'desc' => 'Pesanan sedang dipersiapkan',
                    'icon' => 'av_timer',
                ];
                break;
            case self::COMPLETE:
                return [
                    'label' => 'Selesai',
                    'title' => 'Pesanan Selesai',
                    'desc' => 'Pesanan selesai',
                    'icon' => 'receipt_long',
                ];
                break;

            case self::AWAITING_PICKUP:
                return [
                    'label' => 'Menunggu Diambil',
                    'title' => 'Menunggu Diambil',
                    'desc' => 'Pesanan menungu di ambil oleh pelanggan',
                    'icon' => 'moped',
                ];
                break;

            default:
                $exp = Carbon::parse($this->expired_at)->translatedFormat('l, d M Y H:i:s');
                $msg = 'Menuggu pembayaran dari pembeli, batas pembayaran '.$exp;

                return [
                    'label' => 'Pending',
                    'title' => 'Menunggu Pembayaran',
                    'desc' => $msg,
                    'icon' => 'payments',
                ];
                break;
        }
    }

    public function getCustomerStatusAttribute()
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
                break;
            case self::TOSHIP:
                $msg = 'Pesanan sedang dikemas, menunggu diserahkan ke ekspedisi.';
                if ($this->shipping_type == self::SHIPPING_COD) {
                    $msg =
                       'Pesanan sedang dikemas, menunggu dikirim oleh kurir kami.';
                }

                return [
                    'label' => 'Dikemas',
                    'title' => 'Pesanan Dikemas',
                    'desc' => $msg,
                    'icon' => 'move_to_inbox',
                ];
                break;
            case self::TO_PROCESS:
                $transaction = $this->transaction;
                if ($transaction && $transaction->status == 'UNPAID' && $transaction->payment_proof) {
                    return [
                        'label' => 'Bukti pembayaran dikirim',
                        'title' => 'Menunggu Verifikasi Pembayaran',
                        'desc' => 'Pembayaran sedang di cek, setelah valid pesanan akan segera kami proses',
                        'icon' => 'payments',
                    ];
                }

                return [
                    'label' => 'Sedang Diproses',
                    'title' => 'Pesanan sedang diproses',
                    'desc' => 'Pesanan sedang dipersiapkan',
                    'icon' => 'av_timer',
                ];
                break;
            case self::SHIPPING:
                return [
                    'label' => 'Dikirim',
                    'title' => 'Pesanan Dikirim',
                    'desc' => 'Pesanan sedang dalam proses pengiriman',
                    'icon' => 'local_shipping',
                ];
                break;
            case self::COMPLETE:
                return [
                    'label' => 'Selesai',
                    'title' => 'Pesanan Selesai',
                    'desc' => 'Pesanan selesai, terima kasih telah berbelanja',
                    'icon' => 'receipt_long',
                ];
                break;
            case self::AWAITING_PICKUP:
                return [
                    'label' => 'Menunggu Diambil',
                    'title' => 'Menunggu Diambil',
                    'desc' => 'Silahkan datang dan ambil pesanan anda di toko kami',
                    'icon' => 'moped',
                ];
                break;

            default:
                $exp = Carbon::parse($this->expired_at)->translatedFormat('l, d M Y H:i');

                return [
                    'label' => 'Belum Bayar',
                    'title' => 'Menunggu Pembayaran',
                    'desc' => 'Lakukan pembayaran agar pesananmu dapat diproses, Pembayaran akan kadaluarsa pada '.$exp,
                    'icon' => 'payments',
                ];
                break;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $prefix = 'INV';
            if ($model->product_type == ProductTypeEnum::Deposit->value) {
                $prefix = 'DPO';
            }
            $model->order_ref = $prefix.Carbon::now()->format('ym').str_pad($model->id, 6, '0', STR_PAD_LEFT).Str::upper(Str::random(3));
            $model->save();

            $model->pushHistory('Pesanan dibuat');

            self::clearCache();
        });

        static::updated(function ($model) {
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

        if ($sync) {
            if (! Cache::has('is_cron')) {
                DispatchEventJob::dispatch($event);
            }
        }
    }

    public function getCanConfirmPaymentAttribute()
    {
        $transaction = $this->transaction;

        if (! $transaction) {
            return false;
        }

        if ($this->product_type == ProductTypeEnum::Default->value && in_array($this->order_status, [self::PENDING, self::TO_PROCESS]) && $transaction->status == Transaction::UNPAID) {
            return true;
        }
        if (
            in_array($this->order_status, [self::PENDING, self::TO_PROCESS]) &&
            $transaction->payment_type != 'COD' &&
            in_array($transaction->status, ['UNPAID'])
        ) {
            return true;
        }

        return false;
    }

    public function getCanCompletedAttribute()
    {
        return in_array($this->order_status, [self::SHIPPING, self::AWAITING_PICKUP, self::TO_PROCESS]);
    }

    public function canTrackingWaybill()
    {
        if ($this->product_type != ProductTypeEnum::Default->value) {
            return false;
        }
        if (! $this->shipping_courier_id) {
            return false;
        }
        if (! $this->shipping_courier_code) {
            return false;
        }
        if ($this->shipping_type != self::SHIPPING_COURIER) {
            return false;
        }
        if (str_contains($this->shipping_courier_id, 'gojek') || str_contains($this->shipping_courier_id, 'grab')) {
            return false;
        }
        if (! $this->received_at) {
            return true;
        }

        return false;
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
            ['label' => 'Perlu Dikirim', 'value' => self::TOSHIP],
            ['label' => 'Dikirim', 'value' => self::SHIPPING],
            ['label' => 'Menunggu Diambil', 'value' => self::AWAITING_PICKUP],
            ['label' => 'Selesai', 'value' => self::COMPLETE],
            ['label' => 'Batal', 'value' => self::CANCELED],
        ];
    }

    public static function eventOptions()
    {
        return [
            ['label' => 'PENDING', 'value' => self::PENDING],
            ['label' => 'TOSHIP', 'value' => self::TOSHIP],
            ['label' => 'SHIPPING', 'value' => self::SHIPPING],
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
        Cache::forget('latest_orders');
        Cache::forget('order_reports');
        Cache::forget('transaction_reports');
    }

    public function update_stock($returned = false)
    {
        foreach ($this->items as $item) {

            $product = Product::where('sku', $item->sku)->first();
            $is_unlimited_stock = false;

            if ($product && $product->is_unlimited_stock) {
                $is_unlimited_stock = true;
            }

            if ($is_unlimited_stock == false) {

                if (! $product) {
                    $product = ProductVarian::where('sku', $item->sku)->first();
                }
                if ($product) {
                    if ($returned) {
                        $product->stock += $item->quantity;
                    } else {
                        if ($product->stock > 0) {
                            $product->stock -= $item->quantity;
                        }
                    }
                    if ($product->stock < 0) {
                        $product->stock = 0;
                    }
                    $product->save();

                    Cache::forget('product_'.$product->slug);
                    Cache::forget('product_'.$product->id);
                }
            }
        }
    }
}
