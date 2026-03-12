<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="theme-color" content="#0f172a">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link rel="preload" href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
   <noscript>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
   </noscript>
   <link rel="icon" type="image/png" sizes="32x32" href="{{ url('icon/icon-32x32.png') }}">
   <link rel="icon" type="image/png" sizes="16x16" href="{{ url('icon/icon-16x16.png') }}">
   @include('partial/meta_head')
   @include('partial/json_schema')
   @include('partial/meta_seo')
   <style>
      :root {
         --bg: #f2f5f9;
         --card: #ffffff;
         --text: #0f172a;
         --muted: #5b667a;
         --primary: #0a7b5b;
         --primary-strong: #075d45;
         --ring: rgba(10, 123, 91, 0.16);
         --border: #dfe5ee;
         --soft: #e9eef5;
      }
      * { box-sizing: border-box; }
      body {
         margin: 0;
         background:
            radial-gradient(80rem 30rem at 0% -10%, #dbeafe 0%, rgba(219, 234, 254, 0) 60%),
            radial-gradient(70rem 20rem at 100% -20%, #dcfce7 0%, rgba(220, 252, 231, 0) 60%),
            var(--bg);
         color: var(--text);
         font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      }
      .wrap { max-width: 1080px; margin: 0 auto; padding: 16px; }
      .hero {
         border: 1px solid var(--border);
         background: linear-gradient(135deg, #0f172a, #1f2937 55%, #0b5f46);
         color: #f8fafc;
         border-radius: 24px;
         padding: 22px 18px;
         box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
      }
      .hero-top { display: flex; gap: 14px; align-items: center; }
      .logo {
         width: 62px;
         height: 62px;
         border-radius: 50%;
         border: 2px solid rgba(255, 255, 255, 0.5);
         object-fit: cover;
         background: rgba(255, 255, 255, 0.12);
      }
      .shop-name { margin: 0; font: 800 1.2rem/1.2 "Manrope", sans-serif; letter-spacing: 0.02em; }
      .shop-desc { margin: 6px 0 0; font-size: 0.9rem; color: rgba(248, 250, 252, 0.85); }
      .bio-links { display: grid; gap: 10px; margin-top: 18px; }
      .bio-link {
         display: flex;
         align-items: center;
         justify-content: space-between;
         text-decoration: none;
         color: #f8fafc;
         border: 1px solid rgba(248, 250, 252, 0.25);
         background: rgba(255, 255, 255, 0.06);
         border-radius: 14px;
         padding: 12px 14px;
         font-weight: 700;
      }
      .bio-link:hover { background: rgba(255, 255, 255, 0.12); }
      .section-title {
         margin: 22px 0 12px;
         font: 800 1.08rem/1.3 "Manrope", sans-serif;
         letter-spacing: 0.02em;
      }
      .filters {
         display: flex;
         gap: 8px;
         overflow-x: auto;
         padding-bottom: 8px;
         margin-bottom: 12px;
      }
      .filters::-webkit-scrollbar { height: 6px; }
      .filters::-webkit-scrollbar-thumb { background: #cfd8e5; border-radius: 999px; }
      .chip {
         white-space: nowrap;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         text-decoration: none;
         padding: 8px 12px;
         border-radius: 999px;
         border: 1px solid var(--border);
         background: var(--card);
         color: var(--text);
         font-size: 0.84rem;
         font-weight: 600;
      }
      .chip.active {
         border-color: var(--primary);
         background: var(--ring);
         color: var(--primary-strong);
      }
      .grid {
         display: grid;
         grid-template-columns: 1fr;
         gap: 12px;
      }
      .card {
         background: var(--card);
         border: 1px solid var(--border);
         border-radius: 16px;
         overflow: hidden;
         display: flex;
         flex-direction: column;
         min-height: 100%;
      }
      .thumb-wrap {
         aspect-ratio: 16 / 9;
         background: var(--soft);
      }
      .thumb {
         width: 100%;
         height: 100%;
         object-fit: cover;
         display: block;
      }
      .body { padding: 12px; display: flex; flex-direction: column; gap: 8px; }
      .category-badge {
         width: fit-content;
         background: #eff6ff;
         color: #1d4ed8;
         font-size: 0.72rem;
         font-weight: 700;
         border-radius: 999px;
         padding: 4px 8px;
      }
      .name {
         margin: 0;
         font: 700 0.96rem/1.35 "Manrope", sans-serif;
      }
      .desc {
         margin: 0;
         font-size: 0.84rem;
         color: var(--muted);
         min-height: 2.6em;
      }
      .price {
         margin-top: 4px;
         font: 800 1rem/1.2 "Manrope", sans-serif;
         color: #111827;
      }
      .cta {
         margin-top: 6px;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         text-decoration: none;
         background: var(--primary);
         color: #fff;
         border-radius: 10px;
         padding: 10px 12px;
         font-size: 0.84rem;
         font-weight: 700;
         border: 1px solid var(--primary);
      }
      .cta:hover { background: var(--primary-strong); }
      .empty {
         border: 1px dashed #c8d3e1;
         border-radius: 14px;
         background: #fff;
         padding: 18px;
         color: var(--muted);
         text-align: center;
      }
      .pager {
         margin-top: 14px;
         display: flex;
         justify-content: center;
         gap: 10px;
      }
      .pager-link {
         text-decoration: none;
         border: 1px solid var(--border);
         background: #fff;
         color: var(--text);
         border-radius: 10px;
         padding: 8px 12px;
         font-size: 0.82rem;
         font-weight: 700;
      }
      @media (min-width: 520px) {
         .wrap { padding: 20px; }
         .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      }
      @media (min-width: 900px) {
         .wrap { padding: 28px; }
         .hero { padding: 26px 24px; }
         .shop-name { font-size: 1.35rem; }
         .grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
      }
   </style>
</head>
<body>
@include('partial/meta_body')
@php
   $shop = $jsapp['shop'];
@endphp
<main class="wrap">
   <section class="hero">
      <div class="hero-top">
         <img class="logo" src="{{ $shop->logo_path ? url($shop->logo_path) : url('/static/no_image.png') }}" alt="{{ $shop->name }}">
         <div>
            <h1 class="shop-name">{{ $shop->name }}</h1>
            <p class="shop-desc">{{ $shop->description ?: 'Katalog produk digital pilihan untuk kebutuhan Anda.' }}</p>
         </div>
      </div>

      @if($quickLinks->count() > 0)
         <nav class="bio-links" aria-label="Quick links">
            @foreach($quickLinks as $link)
               <a class="bio-link" href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">
                  <span>{{ $link->provider }}</span>
                  <span aria-hidden="true">-></span>
               </a>
            @endforeach
         </nav>
      @endif
   </section>

   <h2 class="section-title">Katalog Produk</h2>

   <section class="filters" aria-label="Filter kategori">
      <a class="chip {{ $selectedCategory === 'all' ? 'active' : '' }}" href="{{ url('/') }}">Semua</a>
      @foreach($categories as $category)
         <a
            class="chip {{ $selectedCategory === $category->slug ? 'active' : '' }}"
            href="{{ url('/?category=' . $category->slug) }}"
         >
            {{ $category->title }}
         </a>
      @endforeach
   </section>

   @if($products->count() > 0)
      <section class="grid">
         @foreach($products as $index => $product)
            @php
               $asset = $product->assets->first();
               $thumb = $asset ? $asset->src : url('/static/no_image.png');
               $teaser = \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 80);
            @endphp
            <article class="card">
               <div class="thumb-wrap">
                  <img
                     class="thumb"
                     src="{{ $thumb }}"
                     alt="{{ $product->title }}"
                     loading="{{ $index < 2 ? 'eager' : 'lazy' }}"
                     decoding="async"
                     fetchpriority="{{ $index < 2 ? 'high' : 'auto' }}"
                  >
               </div>
               <div class="body">
                  @if($product->category)
                     <span class="category-badge">{{ $product->category->title }}</span>
                  @endif
                  <h3 class="name">{{ $product->title }}</h3>
                  <p class="desc">{{ $teaser ?: 'Lihat detail produk untuk informasi lengkap.' }}</p>
                  <div class="price">{{ money_format_idr($product->price) }}</div>
                  <a
                     class="cta"
                     href="{{ route('product.show', $product->slug) }}"
                     data-product-link="1"
                     data-product-id="{{ $product->id }}"
                     data-product-slug="{{ $product->slug }}"
                     data-category-id="{{ $product->category?->id }}"
                     data-category-slug="{{ $product->category?->slug }}"
                  >
                     Buka Landing Page
                  </a>
               </div>
            </article>
         @endforeach
      </section>

      @if($products->hasPages())
         <nav class="pager" aria-label="Pagination katalog">
            @if($products->onFirstPage())
               <span class="pager-link" aria-disabled="true">Sebelumnya</span>
            @else
               <a class="pager-link" href="{{ $products->previousPageUrl() }}">Sebelumnya</a>
            @endif

            @if($products->hasMorePages())
               <a class="pager-link" href="{{ $products->nextPageUrl() }}">Berikutnya</a>
            @else
               <span class="pager-link" aria-disabled="true">Berikutnya</span>
            @endif
         </nav>
      @endif
   @else
      <div class="empty">
         Produk belum tersedia untuk kategori ini.
      </div>
   @endif
</main>

<script>
   (function () {
      const endpoint = "{{ url('/api-public/product-click') }}";

      function sendTracking(payload) {
         if (window.gtag) {
            window.gtag('event', 'select_content', {
               content_type: 'product',
               item_id: payload.product_slug || ''
            });
         }

         if (window.fbq) {
            window.fbq('trackCustom', 'ProductLinkClick', {
               product_slug: payload.product_slug || ''
            });
         }

         if (navigator.sendBeacon) {
            const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
            navigator.sendBeacon(endpoint, blob);
            return;
         }

         fetch(endpoint, {
            method: 'POST',
            headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json'
            },
            body: JSON.stringify(payload),
            keepalive: true,
            credentials: 'omit'
         }).catch(function () {});
      }

      document.querySelectorAll('[data-product-link]').forEach(function (el) {
         el.addEventListener('click', function (event) {
            event.preventDefault();
            const payload = {
               product_id: parseInt(el.dataset.productId || '', 10) || null,
               product_slug: el.dataset.productSlug || null,
               category_id: parseInt(el.dataset.categoryId || '', 10) || null,
               category_slug: el.dataset.categorySlug || null,
               source: 'home-biolink',
               referrer: window.location.href
            };

            sendTracking(payload);
            window.setTimeout(function () {
               window.location.href = el.href;
            }, 90);
         });
      });
   })();
</script>
</body>
</html>
