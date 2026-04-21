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
            ])
            ->orderByDesc('id')
            ->get();
        return response()->json(ProductResource::collection($products)->resolve());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $product = Product::query()->create($data);
        return response()->json(ProductResource::make($product)->resolve());
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $this->validated($request, $product->id);
        $product->update($data);
        return response()->json(ProductResource::make($product)->resolve());
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json([], 200);
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
            'cost' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:512'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }
}
