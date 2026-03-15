<?php

namespace Database\Seeders;

use App\Enums\ProductTypeEnum;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DigitalProductDummySeeder extends Seeder
{
   public function run(): void
   {
      $items = [
         [
            'category' => ['title' => 'Template', 'slug' => 'template', 'weight' => 1],
            'product' => [
               'title' => 'Template Landing Page Figma – SaaS Starter',
               'price' => 149000,
               'description' => '<p>Template landing page Figma untuk produk SaaS. Termasuk komponen UI reusable, style guide, dan layout responsif.</p>',
               'thumbnail' => 'static/material.png',
               'product_type' => ProductTypeEnum::DigitalDownload->value,
            ],
         ],
         [
            'category' => ['title' => 'Plugin', 'slug' => 'plugin', 'weight' => 2],
            'product' => [
               'title' => 'Plugin WordPress – SEO Booster',
               'price' => 99000,
               'description' => '<p>Plugin WordPress untuk optimasi SEO dasar: meta title/description, sitemap, dan schema markup sederhana.</p>',
               'thumbnail' => 'static/category-other.png',
               'product_type' => ProductTypeEnum::DigitalDownload->value,
            ],
         ],
         [
            'category' => ['title' => 'E-book', 'slug' => 'e-book', 'weight' => 3],
            'product' => [
               'title' => 'E-book: Panduan Bisnis Online 2026',
               'price' => 59000,
               'description' => '<p>E-book praktis untuk memulai bisnis online: riset niche, strategi konten, dan optimasi konversi.</p>',
               'thumbnail' => 'static/location.png',
               'product_type' => ProductTypeEnum::DigitalDownload->value,
            ],
         ],
         [
            'category' => ['title' => 'Kursus Online', 'slug' => 'kursus-online', 'weight' => 4],
            'product' => [
               'title' => 'Kursus Online: Laravel & Vue untuk Toko Digital',
               'price' => 299000,
               'description' => '<p>Kursus step-by-step membangun toko digital: setup project, API, manajemen produk, dan integrasi frontend.</p>',
               'thumbnail' => 'static/warehouse.png',
               'product_type' => ProductTypeEnum::DigitalDownload->value,
            ],
         ],
         [
            'category' => ['title' => 'Software', 'slug' => 'software', 'weight' => 5],
            'product' => [
               'title' => 'Software Invoice – Desktop (Windows)',
               'price' => 199000,
               'description' => '<p>Aplikasi invoice sederhana untuk Windows: template invoice, export PDF, dan rekap transaksi.</p>',
               'thumbnail' => 'static/delivery.png',
               'product_type' => ProductTypeEnum::DigitalDownload->value,
            ],
         ],
         [
            'category' => ['title' => 'Aset Digital', 'slug' => 'aset-digital', 'weight' => 6],
            'product' => [
               'title' => 'Bundle 200 Icon SVG – Minimal',
               'price' => 79000,
               'description' => '<p>Paket ikon SVG minimal untuk UI/website. Cocok untuk dashboard, aplikasi, dan landing page.</p>',
               'thumbnail' => 'static/walet.png',
               'product_type' => ProductTypeEnum::DigitalDownload->value,
            ],
         ],
      ];

      foreach ($items as $item) {
         $category = Category::updateOrCreate(
            ['slug' => $item['category']['slug']],
            [
               'title' => $item['category']['title'],
               'description' => $item['category']['title'] . ' digital untuk ditampilkan di homepage.',
               'is_front' => true,
               'weight' => $item['category']['weight'],
               'category_id' => null,
               'filename' => null,
               'banner' => null,
               'is_background_banner' => 0,
            ]
         );

         $title = $item['product']['title'];
         $slug = Str::slug($title);

         $product = Product::updateOrCreate(
            ['slug' => $slug],
            [
               'title' => $title,
               'description' => $item['product']['description'],
               'stock' => 999999,
               'price' => $item['product']['price'],
               'sold' => 0,
               'status' => true,
               'category_id' => $category->id,
               'weight' => 0,
               'product_type' => $item['product']['product_type'],
            ]
         );

         $filepath = ltrim($item['product']['thumbnail'], '/');
         $asset = Asset::firstOrCreate(
            ['filepath' => $filepath],
            [
               'filename' => basename($filepath),
               'disk' => 'upload',
               'visibility' => 'public',
               'variable' => null,
               'assetable_type' => null,
               'assetable_id' => null,
            ]
         );

         $product->assets()->syncWithoutDetaching([
            $asset->id => ['sort' => 1],
         ]);
      }

      Cache::forget('categories');
      Cache::forget('filter_product_limit');
      Cache::forget('shop_config');
   }
}

