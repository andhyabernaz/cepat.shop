<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
   /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
   public function authorize()
   {
      return true;
   }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
   public function rules()
   {
      $rule = [
         'product_type' => ['required'],
         'customer_name' => ['required', 'string'],
         'customer_phone' => ['required', 'string'],
         'customer_email' => ['nullable', 'email'],
         'payment_type' =>  ['required', 'string'],
         'payment_method' =>  ['required', 'string'],
         'payment_name' =>  ['required', 'string'],
         'payment_code' => ['nullable'],
         'order_items' => ['required', 'array'],
         'order_qty' => ['required', 'numeric'],
         'order_weight' => ['required', 'numeric'],
         'order_unique_code' => ['required', 'numeric'],
         'order_subtotal' => ['required', 'numeric'],
         'order_total' => ['required', 'numeric'],
         'grand_total' => ['required', 'numeric'],
         'shipping_address' => ['required'],
         'shipping_courier_id' => ['required'],
         'shipping_courier_name' => ['required'],
         'shipping_courier_service' => ['required'],
         'shipping_cost' => ['required', 'numeric'],
         'payment_fee' => ['required', 'numeric'],
         'service_fee' => ['required', 'numeric'],
         'voucher_discount' => ['required', 'numeric'],
         'shipping_discount' => ['required', 'numeric'],
      ];

      if (request()->get('product_type') != Product::PRODUCT_DEFAULT) {
         $rule['shipping_address']           = ['nullable'];
         $rule['shipping_courier_id']        = ['nullable'];
         $rule['shipping_courier_name']      = ['nullable'];
         $rule['shipping_courier_service']   = ['nullable'];
      }

      return $rule;
   }
}
