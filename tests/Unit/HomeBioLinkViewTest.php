<?php

namespace Tests\Unit;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Tests\TestCase;

class HomeBioLinkViewTest extends TestCase
{
    public function test_home_view_renders_product_card_and_filter(): void
    {
        $product = new Fluent([
            'id' => 77,
            'slug' => 'paket-ultimate',
            'title' => 'Paket Ultimate',
            'description' => 'Deskripsi singkat produk digital.',
            'price' => 125000,
            'assets' => new Collection([
                new Fluent(['src' => 'https://shop.cepat.digital/local/thumb.webp']),
            ]),
            'category' => new Fluent([
                'id' => 5,
                'slug' => 'kelas-online',
                'title' => 'Kelas Online',
            ]),
        ]);

        $products = new LengthAwarePaginator(
            new Collection([$product]),
            1,
            12,
            1,
            ['path' => '/']
        );

        $jsapp = [
            'page' => [
                'title' => 'Demo Shop',
                'description' => 'Demo desc',
                'featured_image' => null,
            ],
            'shop' => new Fluent([
                'name' => 'Demo Shop',
                'description' => 'Demo desc',
                'logo_path' => null,
            ]),
            'head' => [
                'fb_pixel' => null,
                'gtm' => null,
                'custom_css' => null,
            ],
        ];

        $categories = new Collection([
            new Fluent(['id' => 5, 'slug' => 'kelas-online', 'title' => 'Kelas Online']),
        ]);

        $quickLinks = new Collection([
            new Fluent(['provider' => 'Shopee', 'url' => 'https://example.com']),
        ]);

        $html = view('home', [
            'jsapp' => $jsapp,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => 'all',
            'quickLinks' => $quickLinks,
        ])->render();

        $this->assertStringContainsString('Katalog Produk', $html);
        $this->assertStringContainsString('Paket Ultimate', $html);
        $this->assertStringContainsString('/product/paket-ultimate', $html);
        $this->assertStringContainsString('Kelas Online', $html);
        $this->assertStringContainsString('data-product-link="1"', $html);
    }
}
