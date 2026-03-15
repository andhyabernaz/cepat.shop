<?php

namespace Tests\Unit;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductStockHandlingTest extends TestCase
{
    public function test_product_request_allows_unlimited_stock_as_null(): void
    {
        $payload = $this->validPayload([
            'simple_product' => true,
            'stock' => null,
        ]);

        $validator = Validator::make(
            $payload,
            ProductRequest::create('/', 'POST', $payload)->rules()
        );

        $this->assertTrue($validator->passes());
    }

    public function test_product_request_rejects_non_positive_limited_stock(): void
    {
        $payload = $this->validPayload([
            'simple_product' => true,
            'stock' => 0,
        ]);

        $validator = Validator::make(
            $payload,
            ProductRequest::create('/', 'POST', $payload)->rules()
        );

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('stock', $validator->errors()->messages());
    }

    public function test_product_request_rejects_non_positive_variant_stock(): void
    {
        $payload = $this->validPayload([
            'simple_product' => false,
            'varians' => [
                [
                    'label' => 'Lisensi',
                    'value' => 'Personal',
                    'stock' => 0,
                ],
            ],
        ]);

        $validator = Validator::make(
            $payload,
            ProductRequest::create('/', 'POST', $payload)->rules()
        );

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('varians.0.stock', $validator->errors()->messages());
    }

    public function test_product_resource_returns_null_stock_for_unlimited_product(): void
    {
        $product = new Product([
            'id' => 10,
            'title' => 'Produk Unlimited',
            'slug' => 'produk-unlimited',
            'description' => 'Stok tidak terbatas',
            'stock' => null,
            'price' => 25000,
            'weight' => 100,
            'product_type' => 'Digital',
        ]);

        $product->setRelation('assets', collect());
        $product->setRelation('productPromo', null);
        $product->setRelation('varianItemSortByPrice', collect());
        $product->setRelation('varianAttributes', collect());

        $data = (new ProductResource($product))->toArray(request());

        $this->assertNull($data['stock']);
        $this->assertTrue($data['is_unlimited_stock']);
    }

    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Produk Uji',
            'product_type' => 'Digital',
            'price' => 15000,
            'description' => 'Deskripsi produk',
            'assets' => [
                ['id' => 1],
            ],
        ], $overrides);
    }
}
