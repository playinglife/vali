<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);

        $variant = $product->Variants()->create($data);

        $this->syncValues($variant, $request, $product);

        return back()->with('status', 'Variant created.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $data = $this->validated($request, $variant->id);

        $variant->update($data);

        $this->syncValues($variant, $request, $product);

        return back()->with('status', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variant->delete();

        return back()->with('status', 'Variant deleted.');
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
}
