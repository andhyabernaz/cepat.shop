<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Page;
use App\Services\Media\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function __construct(
        protected MediaService $mediaService
    ) {}

    public function index(Request $request)
    {
        $search = $request->query('search');

        $data = Page::query()
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            })
            ->with('asset')
            ->latest()
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        return ApiResponse::success($data);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $page = Page::create($validated);

        if ($file = $request->file('featured_image')) {
            $filedata = $this->mediaService->storeFile($file);
            $page->asset()->create($filedata);
        }

        return ApiResponse::success($page->load('asset'));
    }

    public function show($id)
    {
        $data = Page::with('asset')->findOrFail($id);
        return ApiResponse::success($data);
    }

    public function update(Request $request, $id)
    {
        $page = Page::with('asset')->findOrFail($id);

        $validated = $this->validatePayload($request, $page->id);

        $removeImage = $request->boolean('remove_featured_image');
        $file = $request->file('featured_image');

        if ($removeImage && $page->asset) {
            $this->mediaService->deleteAsset($page->asset);
            $page->unsetRelation('asset');
        }

        if ($file) {
            if ($page->asset) {
                $this->mediaService->deleteAsset($page->asset);
            }
            $filedata = $this->mediaService->storeFile($file);
            $page->asset()->create($filedata);
        }

        $page->update($validated);

        return ApiResponse::success($page->fresh()->load('asset'));
    }

    public function destroy($id)
    {
        $page = Page::with('asset')->findOrFail($id);

        if ($page->asset) {
            $this->mediaService->deleteAsset($page->asset);
        }

        $page->delete();

        return ApiResponse::success();
    }

    protected function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $slug = $request->input('slug');
        if (! $slug && $request->input('title')) {
            $request->merge(['slug' => Str::slug($request->input('title'))]);
        }

        $reserved = ['api', 'api-public', 'admin', 'auth', 'me', 'install', 'p', 'product', 'products', 'post', 'posts', 'cart'];

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn($reserved),
                Rule::unique('pages', 'slug')->ignore($ignoreId),
            ],
            'category' => ['nullable', 'string', 'max:120'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status' => ['required', 'string', Rule::in(['draft', 'published'])],
            'theme' => ['nullable', 'string', Rule::in(['light', 'dark'])],
            'pixel_id' => ['nullable', 'string', 'max:255'],
            'pixel_access_token' => ['nullable', 'string'],
            'pixel_test_event_code' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

