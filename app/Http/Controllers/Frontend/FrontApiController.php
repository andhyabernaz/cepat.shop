<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\ApiResponse;
use App\Models\AffiliateConfig;
use App\Models\BankAccount;
use App\Models\Block;
use App\Models\Category;
use App\Models\Config;
use App\Models\Marketplace;
use App\Models\Post;
use App\Models\Slider;
use App\Models\Store;
use App\Models\Voucher;
use App\Services\Product\PublicProductService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FrontApiController extends Controller
{
    public function __construct(protected PublicProductService $service)
    {
    }

    public function clearCache()
    {
        Cache::flush();

        return ApiResponse::success();
    }

    public function getInitialData()
    {
        $data['sliders'] = Cache::rememberForever('sliders', function () {
            return Slider::OrderBy('weight', 'asc')->with('post')->get();
        });

        $data['blocks'] = Cache::rememberForever('blocks', function () {
            return Block::with('post:id,title,slug')
                ->OrderBy('weight', 'asc')
                ->get();
        });

        $data['shop'] = Cache::rememberForever('shop', function () {
            return Store::first();
        });

        $data['categories'] = Cache::remember('categories', now()->addMinutes(30), function () {
            return Category::withChilds()
                ->withCount('childProducts')
                ->withCount('products')
               // ->whereHas('products')
               // ->orWhereHas('childProducts')
                ->get();
        });

        $data['config'] = Cache::rememberForever('shop_config', function () {
            return Config::first();
        });

        $data['post_promote_count'] = Cache::remember('post_promote_count', now()->addHour(), function () {
            return Post::promote()->count();
        });

        $data['product_promo'] = Cache::remember('product_promo', now()->addMinutes(3), function () {
            return $this->service->getProductPromo();
        });

        $data['sess_id'] = Str::random(49);

        return ApiResponse::success($data);
    }

    public function getSliders()
    {
        $data = Cache::rememberForever('sliders', function () {
            return Slider::OrderBy('weight', 'asc')->get();
        });

        return ApiResponse::success($data);
    }

    public function getShop()
    {
        $data['shop'] = Cache::rememberForever('shop', function () {
            return Store::first();
        });
        $data['config'] = Cache::rememberForever('shop_config', function () {
            return Config::first();
        });

        return ApiResponse::success($data);
    }

    public function getCategories(Request $request)
    {
        $data = [];

        if ($request->with) {
            if ($request->with == 'parent') {
                $data = Category::withParent()->get();
            }
            if ($request->with == 'child') {
                $data = Category::withChilds()->get();
            }
        } elseif ($request->only) {
            if ($request->only == 'parent') {
                $data = Category::onlyParents()->get();
            }
            if ($request->only == 'child') {
                $data = Category::onlyChilds()->get();
            }
        } else {

            $data = Category::all();
        }

        return ApiResponse::success($data);
    }

    public function getAllCategories()
    {
        $data = Category::all();

        return ApiResponse::success($data);
    }

    public function showCategory($id)
    {
        $data = Category::find($id);

        return ApiResponse::success($data);
    }

    public function getBlocks()
    {
        $data = Block::with('post:id,slug,title')->orderBy('position', 'desc')->get();

        return ApiResponse::success($data);
    }

    public function showBlock($id)
    {
        $data = Block::with('post')->find($id);

        return ApiResponse::success($data);
    }

    public function getConfig()
    {
        $data = Config::first();
        if ($data) {
            $data->makeHidden(Config::HIDDEN_FIELDS);
        }

        return ApiResponse::success($data);
    }

    public function getBanks()
    {
        $data = BankAccount::all();

        return ApiResponse::success($data);
    }

    public function getPromotePosts()
    {
        $data = Cache::rememberForever('promote_post', function () {
            return Post::promote()->with('asset')->latest()->get();
        });

        return ApiResponse::success($data);
    }

    public function getPosts(Request $request)
    {
        $cacheKey = 'post_'.http_build_query($request->all());

        $posts = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {

            return Post::when($request->tags && $request->tags != 'all', function ($q) use ($request) {
                $q->where('tags', $request->tags);
            })->when($request->q, function ($q) use ($request) {
                if ($request->q == 'listing') {
                    $q->listing();
                }
                if ($request->q == 'promote') {
                    $q->promote();
                }
            })
                ->with('asset')->latest()->paginate($request->per_page ?? 6)->withQueryString();
        });

        return ApiResponse::success($posts);
    }

    public function getRelatedPost($id)
    {
        $post = Post::select('id', 'tags')->where('id', $id)->first();
        if (! $post) {
            return ApiResponse::success([]);
        }

        $posts = Post::where('tags', $post->tags)
            ->where('id', '<>', $id)
            ->inRandomOrder()
            ->with('asset')->limit(6)->get();

        return ApiResponse::success($posts);
    }

    public function postTags()
    {
        $tags = Post::select('tags')->whereNotNull('tags')->groupBy('tags')->get()->map(function ($tag) {
            return $tag->tags;
        });

        return ApiResponse::success($tags);
    }

    public function getPostDetail($slug)
    {
        $data = Post::where('slug', $slug)->with('asset', 'user')->first();

        return ApiResponse::success($data);
    }

    public function runCommand(Request $request)
    {
        $request->validate([
            'command' => 'required',
            'key' => 'required',
        ]);

        if ($request->key != env('APP_KEY')) {
            return ApiResponse::failed('Key invalid');
        }

        Artisan::call($request->command);

        return ApiResponse::success();
    }

    public function getVoucherActive()
    {
        $data = Voucher::withCount('orders')->active()->get();

        return ApiResponse::success($data);
    }

    public function getVoucherByCode($voucher_code)
    {
        $data = Voucher::withCount('orders')->where('voucher_code', $voucher_code)->active()->first();

        return ApiResponse::success($data);
    }

    public function marketplaces()
    {
        $data = Marketplace::where('is_active', 1)->whereNotNull('url')->get();

        return ApiResponse::success($data);
    }

    public function getAffiliateConfig()
    {
        $data = AffiliateConfig::first();

        return ApiResponse::success($data);
    }
}
