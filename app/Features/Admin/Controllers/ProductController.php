<?php

namespace App\Features\Admin\Controllers;

use App\Features\Admin\Resources\ProductResource;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{

    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with([
                'ProductImages',
                'OptionValues.Option',
                'Variants.VariantImages',
                'Variants.Values',
                'MetaTitleTranslation',
                'MetaDescriptionTranslation',
            ])
            ->orderByDesc('id')
            ->get();
        return response()->json(ProductResource::collection($products)->resolve());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $meta = $this->extractMeta($data);
        $product = Product::query()->create($data);
        $product->syncMetaTranslations($meta);
        $product->load(['MetaTitleTranslation', 'MetaDescriptionTranslation', 'Variants']);

        return response()->json(ProductResource::make($product)->resolve());
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $this->validated($request, $product->id);
        $meta = $this->extractMeta($data);
        $product->update($data);
        $product->syncMetaTranslations($meta);
        $product->load(['MetaTitleTranslation', 'MetaDescriptionTranslation', 'Variants']);

        return response()->json(ProductResource::make($product)->resolve());
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json([], 200);
    }


    public function productDetail(Product $product): View
    {

    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, ?int $productId = null): array
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_title_ro' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string', 'max:512'],
            'meta_description_ro' => ['nullable', 'string', 'max:512'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title_en: string|null, title_ro: string|null, description_en: string|null, description_ro: string|null}
     */
    protected function extractMeta(array &$data): array
    {
        $meta = [
            'title_en' => $data['meta_title_en'] ?? null,
            'title_ro' => $data['meta_title_ro'] ?? null,
            'description_en' => $data['meta_description_en'] ?? null,
            'description_ro' => $data['meta_description_ro'] ?? null,
        ];
        unset(
            $data['meta_title_en'],
            $data['meta_title_ro'],
            $data['meta_description_en'],
            $data['meta_description_ro'],
        );

        return $meta;
    }
}
