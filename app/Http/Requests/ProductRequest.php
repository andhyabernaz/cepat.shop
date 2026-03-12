<?php

namespace App\Http\Requests;

use App\Enums\ProductTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
   public function authorize()
   {
      return true;
   }

   public function rules()
   {
      $rules = [
         'title' => 'required|string|max:190',
         'product_type' => ['required', Rule::in(ProductTypeEnum::getNonPhysicalValues())],
         'price' => 'required',
         'description' => 'required',
         'aff_amount' => 'numeric',
         'assets' => ['required', 'array'],
         'varians' => ['nullable', 'array'],
         'varians.*.stock' => ['nullable', 'integer', 'min:1'],
         'varians.*.subvarian' => ['nullable', 'array'],
         'varians.*.subvarian.*.stock' => ['nullable', 'integer', 'min:1'],
      ];

      if ($this->product_type == ProductTypeEnum::DigitalVideo->value) {
         $rules['digital_videos'] = ['required', 'array'];
      }
      if ($this->product_type == ProductTypeEnum::DigitalDownload->value) {
         $rules['digital_downloads'] = ['required', 'array'];
      }
      if ($this->boolean('simple_product')) {
         $rules['stock'] = ['nullable', 'integer', 'min:1'];
      }

      return $rules;
   }

   public function messages()
   {
      return [
         'title.unique' => 'Nama produk sudah digunakan',
         'stock.min' => 'Stok harus berupa bilangan bulat positif.',
         'varians.*.stock.min' => 'Stok varian harus berupa bilangan bulat positif.',
         'varians.*.subvarian.*.stock.min' => 'Stok subvarian harus berupa bilangan bulat positif.',
      ];
   }

   protected function prepareForValidation(): void
   {
      $this->merge([
         'title' => strip_tags($this->title),
      ]);
   }
}
