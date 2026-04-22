<?php

namespace App\Features\Admin\Controllers;

use App\Features\Admin\Resources\OptionResource;
use App\Features\Admin\Resources\ProductResource;
use App\Features\Admin\Resources\VariantResource;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        $product->load([
            'OptionValues.Option',
            'Variants.Values.Option',
            'Variants.VariantImages',
        ]);
        $options = ProductOption::query()->with('Values')->orderBy('sort_order')->get();
        return response()->json([
            'product' => ProductResource::make($product)->resolve(),
            'options' => OptionResource::collection($options)->resolve(),
        ]);
    }
    public function store(Request $request, Product $product): JsonResponse
    {
        $data = $this->validated($request);
        $variant = $product->Variants()->create($data);
        $this->syncValues($variant, $request, $product);
        $variant->load(['Values.Option', 'VariantImages']);
        return response()->json(VariantResource::make($variant)->resolve());
    }

    public function update(Request $request, Product $product, ProductVariant $variant): JsonResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);
        $data = $this->validated($request, $variant->id);
        $variant->update($data);
        $this->syncValues($variant, $request, $product);
        $variant->load(['Values.Option', 'VariantImages']);
        return response()->json(VariantResource::make($variant)->resolve());
    }

    public function destroy(Product $product, string $variant): JsonResponse
    {
        $variantIds = collect(explode(',', $variant))
            ->map(static fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();
        if ($variantIds->isEmpty()) {
            return response()->json([], 200);
        }
        $matchingCount = ProductVariant::query()
            ->where('product_id', $product->id)
            ->whereIn('id', $variantIds->all())
            ->count();
        if ($matchingCount !== $variantIds->count()) {
            abort(404);
        }
        ProductVariant::query()
            ->where('product_id', $product->id)
            ->whereIn('id', $variantIds->all())
            ->delete();
        return response()->json([], 200);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, ?int $variantId = null): array
    {
        $data = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_variants', 'sku')->ignore($variantId),
            ],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'value_ids' => ['nullable', 'array'],
            'value_ids.*' => ['integer', 'exists:product_option_values,id'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    protected function syncValues(ProductVariant $variant, Request $request, Product $product): void
    {
        $ids = collect($request->input('value_ids', []))
            ->map(static fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            $variant->Values()->sync([]);

            return;
        }

        $allowedIds = $product->OptionValues()
            ->whereIn('product_option_values.id', $ids->all())
            ->pluck('product_option_values.id')
            ->map(static fn ($id) => (int) $id)
            ->all();

        $variant->Values()->sync($allowedIds);
    }

    protected function ensureVariantBelongsToProduct(Product $product, ProductVariant $variant): void
    {
        if ((int) $variant->product_id !== (int) $product->id) {
            abort(404);
        }
    }

    public function createAll(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'option_ids' => ['required', 'array', 'min:1'],
            'option_ids.*' => ['integer'],
        ]);
        $optionIds = collect($validated['option_ids'])
            ->map(static fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();
        $values = $product->OptionValues()
            ->with('Option')
            ->whereIn('product_option_values.product_option_id', $optionIds->all())
            ->get();
        $optionValuesByOptionId = $values
            ->groupBy('product_option_id')
            ->map(fn ($group) => $group->sortBy('sort_order')->values());
        $missingOptionIds = $optionIds
            ->filter(fn ($optionId) => ! $optionValuesByOptionId->has($optionId))
            ->values();
        if ($missingOptionIds->isNotEmpty()) {
            return response()->json([
                'message' => 'Some requested options are not available for this product.',
                'missing_option_ids' => $missingOptionIds->all(),
            ], 422);
        }
        $valueSetsByOption = $optionIds
            ->map(fn ($optionId) => $optionValuesByOptionId->get($optionId, collect()))
            ->filter(fn ($group) => $group->isNotEmpty())
            ->values();
        if ($valueSetsByOption->isEmpty()) {
            return response()->json([
                'created' => 0,
                'variants' => [],
            ]);
        }
        $combinations = collect([[]]);
        foreach ($valueSetsByOption as $group) {
            $combinations = $combinations->flatMap(function (array $current) use ($group) {
                return $group->map(function ($value) use ($current) {
                    $next = $current;
                    $next[] = (int) $value->id;
                    sort($next);
                    return $next;
                });
            })->values();
        }
        $existingSignatures = $product->Variants()
            ->with('Values:id')
            ->get()
            ->mapWithKeys(function (ProductVariant $variant): array {
                $ids = $variant->Values
                    ->pluck('id')
                    ->map(static fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->all();
                return [implode('-', $ids) => true];
            });
        $createdVariantIds = [];
        DB::transaction(function () use ($combinations, $existingSignatures, $product, &$createdVariantIds): void {
            foreach ($combinations as $valueIds) {
                $signature = implode('-', $valueIds);
                if ($existingSignatures->has($signature)) {
                    continue;
                }
                $variant = $product->Variants()->create([
                    'sku' => $this->generateUniqueVariantSku($product, $valueIds),
                    'price' => null,
                    'discount_type' => null,
                    'discount' => null,
                    'stock_quantity' => 0,
                    'weight' => $product->weight,
                    'barcode' => null,
                    'is_active' => true,
                ]);
                $variant->Values()->sync($valueIds);
                $createdVariantIds[] = (int) $variant->id;
                $existingSignatures->put($signature, true);
            }
        });
        $createdVariants = ProductVariant::query()
            ->with(['Values.Option', 'VariantImages'])
            ->whereIn('id', $createdVariantIds)
            ->orderBy('id')
            ->get();
        return response()->json([
            'created' => $createdVariants->count(),
            'variants' => VariantResource::collection($createdVariants)->resolve(),
        ]);
    }

    public function storeImage(Request $request, Product $product, ProductVariant $variant): JsonResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);
        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'max:8192'],
        ]);
        $nextSortOrder = (int) ($variant->VariantImages()->max('sort_order') ?? 0) + 1;
        $variantImage = $variant->VariantImages()->create([
            'sort_order' => $nextSortOrder,
        ]);
        $extension = strtolower((string) $validated['image']->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'svg'];
        if (! in_array($extension, $allowedExtensions, true)) {
            $extension = 'jpg';
        }
        $disk = Storage::disk('public');
        foreach (['jpg', 'jpeg', 'png', 'webp', 'avif', 'svg'] as $ext) {
            $disk->delete('product/variant-images/' . $variantImage->id . '.' . $ext);
        }
        $validated['image']->storeAs('product/variant-images', $variantImage->id . '.' . $extension, 'public');
        $variantImage->refresh();
        return response()->json([
            'image' => $variantImage->image,
            'variant_image_id' => $variantImage->id,
        ]);
    }

    public function destroyImage(Product $product, ProductVariant $variant, ProductVariantImage $image): JsonResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);
        if ((int) $image->product_variant_id !== (int) $variant->id) {
            abort(404);
        }
        $disk = Storage::disk('public');
        foreach (['jpg', 'jpeg', 'png', 'webp', 'avif', 'svg'] as $ext) {
            $disk->delete('product/variant-images/' . $image->id . '.' . $ext);
        }
        $image->delete();
        return response()->json([], 200);
    }

    /**
     * @param array<int, int> $valueIds
     */
    protected function generateUniqueVariantSku(Product $product, array $valueIds): string
    {
        $base = $product->sku . '-' . implode('-', $valueIds);
        $sku = $base;
        $suffix = 1;
        while (ProductVariant::query()->where('sku', $sku)->exists()) {
            $sku = $base . '-' . $suffix;
            $suffix++;
        }
        return $sku;
    }
}
