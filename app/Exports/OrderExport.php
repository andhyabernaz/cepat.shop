<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
   public $status;
   public $start_date;
   public $end_date;

   public function __construct($status, $start_date = null, $end_date = null)
   {
      $this->status = $status;
      $this->start_date = $start_date;
      $this->end_date = $end_date;
   }

   public function collection()
   {
      $instance = DB::table('orders')
         ->when($this->status != 'ALL', function ($q) {
            $q->where('order_status', $this->status);
         });

      if ($this->start_date) {
         $from = Carbon::parse($this->start_date)->startOfDay()->toDateTimeString();
         $to = $this->end_date ? Carbon::parse($this->end_date)->endOfDay()->toDateTimeString() : Carbon::now()->toDateTimeString();
         $instance->whereBetween('orders.created_at', [$from, $to]);
      }

      return $instance->select(
         'orders.created_at',
         'orders.order_ref',
         'orders.customer_name',
         'orders.customer_email',
         'orders.customer_whatsapp',
         'orders.order_status',
         DB::raw("CONCAT(order_items.name, ' ', IFNULL(order_items.note, '')) as product_name"),
         'order_items.price',
         'order_items.quantity',
         'orders.order_subtotal',
         'orders.voucher_discount',
         'orders.order_unique_code',
         'orders.payment_fee',
         'orders.service_fee',
         DB::raw('orders.order_total + orders.payment_fee as billing_total'),
         'transactions.payment_name',
         'transactions.payment_code',
         'orders.note',
      )
         ->join('order_items', 'order_items.order_id', 'orders.id')
         ->join('transactions', 'transactions.order_id', 'orders.id')
         ->get();
   }

   public function headings(): array
   {
      return [
         'Tanggal',
         'No Pesanan',
         'Nama',
         'Email',
         'No Telp',
         'Status',
         'Produk',
         'Item Price',
         'Item Qty',
         'Order Subtotal',
         'Voucher Diskon',
         'Kode Unik',
         'Payment Fee',
         'Jasa Aplikasi',
         'Total bayar',
         'Metode Pembayaran',
         'Kode bayar',
         'Catatan',
      ];
   }
}
