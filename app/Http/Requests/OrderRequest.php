<?php

namespace App\Http\Requests;

use App\Enums\ProductTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
   public function authorize()
   {
      return true;
   }

   public function rules()
   {
      $isDirectCheckout = $this->input('order_source') === 'direct_checkout';

      $rule = [
         'order_source' => ['nullable', 'string'],
         'product_type' => ['required', Rule::in(ProductTypeEnum::getNonPhysicalValues())],
         'customer_name' => ['required', 'string'],
         'customer_phone' => ['required', 'string'],
         'customer_email' => ['required', 'email'],
         'shipping_address' => [$isDirectCheckout ? 'required' : 'nullable', 'string'],
         'shipping_type' => [$isDirectCheckout ? 'required' : 'nullable', 'string'],
         'shipping_courier_id' => [$isDirectCheckout ? 'required' : 'nullable', 'string'],
         'shipping_courier_name' => [$isDirectCheckout ? 'required' : 'nullable', 'string'],
         'shipping_courier_service' => [$isDirectCheckout ? 'required' : 'nullable', 'string'],
         'shipping_cost' => ['required', 'numeric', 'min:0'],
         'payment_type' =>  ['required', 'string'],
         'payment_method' =>  ['required', 'string'],
         'payment_name' =>  ['required', 'string'],
         'payment_code' => ['nullable'],
         'order_items' => ['required', 'array'],
         'order_items.*.product_id' => ['required', 'integer'],
         'order_items.*.sku' => ['required', 'string'],
         'order_items.*.quantity' => ['required', 'integer', 'min:1'],
         'order_qty' => ['required', 'numeric'],
         'order_weight' => ['required', 'numeric'],
         'order_unique_code' => ['required', 'numeric'],
         'order_subtotal' => ['required', 'numeric'],
         'order_total' => ['required', 'numeric'],
         'grand_total' => ['required', 'numeric'],
         'payment_fee' => ['required', 'numeric', 'min:0'],
         'service_fee' => ['required', 'numeric', 'min:0'],
         'voucher_discount' => ['required', 'numeric', 'min:0'],
         'shipping_discount' => ['required', 'numeric', 'min:0'],
         'voucher_id' => ['nullable', 'integer'],
      ];

      return $rule;
   }
}
