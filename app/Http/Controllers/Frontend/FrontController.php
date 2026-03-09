<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class FrontController extends Controller
{
   public $shop;

   public function homepage()
   {
      $this->shop = getShop();
      $title = $this->shop->name;

      if ($this->shop->slogan) {
         $title = $title . ' | ' . $this->shop->slogan;
      }
      return View::vue([
         'title' => $title,
         'description' => $this->shop->description,
         'featured_image' => $this->shop->logo_path ? url($this->shop->logo_path) : null,
      ]);
   }
   public function products()
   {
      $this->shop = getShop();
      return View::vue([
         'title' => 'Produk Katalog | ' . $this->shop->name,
         'description' => $this->shop->description,
         'featured_image' => $this->shop->logo_path ? url($this->shop->logo_path) : null,
      ]);
   }
   public function productDetail($slug)
   {
      $this->shop = getShop();
      $product =  Product::select('id', 'slug', 'title', 'description')->with(['assets'])
         ->withCount('reviews')
         ->withAvg('reviews', 'rating')
         ->where('slug', $slug)
         ->first();
      if (!$product) {
         return redirect('/');
      }

      $desc = $product->description ? createTeaser($product->description) : $this->shop->description;

      $schema = $this->getSingleProductSchema($product);

      return View::vue([
         'title' => $product->title . ' | ' . $this->shop->name,
         'description' => $desc,
         'featured_image' => $product->assets[0]->src,
         'json_schema' => $schema
      ]);
   }
   public function productCategory($id)
   {
      $category = Category::where('slug', $id)->orWhere('id', $id)->first();
      $this->shop = getShop();
      if (!$category) {
         return redirect('/');
      }
      return View::vue([
         'title' => $category->title . ' | ' . $this->shop->name,
         'description' => $category->description ?? $this->shop->description,
         'featured_image' => url('/upload/images/' . $category->filename),
      ]);
   }
   public function postIndex()
   {
      $this->shop = getShop();
      return View::vue([
         'title' => 'Artikel | ' . $this->shop->name,
         'description' => $this->shop->description,
         'featured_image' => $this->shop->logo_path ? url($this->shop->logo_path) : null,
      ]);
   }
   public function postDetail($slug)
   {
      $this->shop = getShop();
      $post = Post::select('id', 'title', 'body', 'image')->with('asset')->where('slug', $slug)->first();

      if (!$post) {
         return redirect('/');
      }

      return View::vue([
         'title' => $post->title . ' | ' . $this->shop->name,
         'description' => createTeaser($post->body),
         'featured_image' => $post->asset ? $post->asset->src : ''
      ]);
   }
   public function showInvoice($id)
   {
      $this->shop = getShop();
      return View::vue([
         'title' => "Invoice #$id",
         'description' => "Detail tagihan dan instruksi pembayaran invoice #$id - " . $this->shop->name,
      ]);
   }
   public function any()
   {
      $this->shop = getShop();
      $title = $this->shop->name;
      if ($this->shop->slogan) {
         $title = $title . ' | ' . $this->shop->slogan;
      }
      return View::vue([
         'title' => $title,
      ]);
   }
   public function clearCache()
   {
      Cache::flush();
      return redirect('/');
   }
   public function sitemap()
   {
      $categories = Category::select('id', 'slug', 'updated_at')->get();
      $products = Product::select('id', 'slug', 'updated_at')->get();
      $posts = Post::select('id', 'slug', 'updated_at')->get();
      return response()->view('sitemap', compact('categories', 'products', 'posts'))
         ->header('Content-Type', 'text/xml');
   }
   protected function getSingleProductSchema($product)
   {
      $desc = $product->description ? createTeaser($product->description) : $this->shop->description;
      $rating = $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : 0;
      $asset = $product->assets[0];

      $data = [
         "@context" => "https://schema.org",
         "@type" => "Product",
         "description" => $desc,
         "name" => $product->title,
         "image" => $asset->src,
         "offers" => [
            "@type" => "Offer",
            "availability" => "https://schema.org/InStock",
            "price" => $product->price,
            "priceCurrency" => "IDR"
         ],
         "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => "$rating",
            "reviewCount" => "$product->reviews_count",
         ],
         "review" => [
            "@type" => "Review",
            "reviewRating" => [
               "@type" => "Rating",
               "ratingValue" => "$rating",
               "bestRating" => "5"
            ]
         ]
      ];

      return json_encode($data, JSON_UNESCAPED_SLASHES);
   }
}
