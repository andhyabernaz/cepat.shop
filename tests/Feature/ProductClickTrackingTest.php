<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductClickTrackingTest extends TestCase
{
    public function test_product_click_endpoint_stores_payload(): void
    {
        config(['installer.installed' => true]);

        DB::shouldReceive('table')->once()->with('product_clicks')->andReturnSelf();
        DB::shouldReceive('insert')->once()->andReturn(true);

        $response = $this->postJson('/api-public/product-click', [
            'product_id' => 10,
            'product_slug' => 'starter-pack',
            'category_id' => 4,
            'category_slug' => 'ebook',
            'source' => 'home-biolink',
            'referrer' => 'https://shop.cepat.digital/',
        ], [
            'Session-User' => 'session-test-id',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_product_click_endpoint_validates_category_id_type(): void
    {
        config(['installer.installed' => true]);

        $response = $this->postJson('/api-public/product-click', [
            'category_id' => 'not-a-number',
        ]);

        $response->assertStatus(422);
    }
}
