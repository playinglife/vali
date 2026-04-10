<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminProductOptionController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'initial_value' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'show_on_products' => ['nullable', 'boolean'],
        ]);

        $option = ProductOption::query()->create([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
            'show_on_products' => $request->boolean('show_on_products'),
        ]);

        $value = $option->Values()->create([
            'value' => $data['initial_value'],
            'sort_order' => 0,
            'price_adjustment_type' => null,
            'price_adjustment' => 0,
        ]);
        $value->Products()->syncWithoutDetaching([$product->id]);

        return back()->with('status', "Option created for {$product->name}.");
    }

    public function update(Request $request, Product $product, ProductOption $option): RedirectResponse
    {
        $this->ensureOptionBelongsToProduct($product, $option);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'show_on_products' => ['nullable', 'boolean'],
        ]);

        $option->update([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
            'show_on_products' => $request->boolean('show_on_products'),
        ]);

        return back()->with('status', 'Option updated.');
    }

    public function destroy(Product $product, ProductOption $option): RedirectResponse
    {
        $this->ensureOptionBelongsToProduct($product, $option);

        foreach ($option->Values as $value) {
            $value->Products()->detach($product->id);
        }

        if (! $option->Values()->whereHas('Products')->exists()) {
            $option->delete();
        }

        return back()->with('status', 'Option removed from product.');
    }

    public function storeValue(Request $request, Product $product, ProductOption $option): RedirectResponse
    {
        $this->ensureOptionBelongsToProduct($product, $option, false);

        $data = $request->validate([
            'value' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'price_adjustment_type' => ['nullable', 'in:fix,percentage'],
            'price_adjustment' => ['nullable', 'numeric', 'min:0'],
        ]);

        $value = $option->Values()->create([
            'value' => $data['value'],
            'sort_order' => $data['sort_order'] ?? 0,
            'price_adjustment_type' => $data['price_adjustment_type'] ?? null,
            'price_adjustment' => $data['price_adjustment'] ?? 0,
        ]);

        $value->Products()->syncWithoutDetaching([$product->id]);

        return back()->with('status', 'Option value created.');
    }

    public function updateValue(Request $request, Product $product, ProductOption $option, ProductOptionValue $value): RedirectResponse
    {
        $this->ensureValueBelongsToProductAndOption($product, $option, $value);

        $data = $request->validate([
            'value' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'price_adjustment_type' => ['nullable', 'in:fix,percentage'],
            'price_adjustment' => ['nullable', 'numeric', 'min:0'],
        ]);

        $value->update([
            'value' => $data['value'],
            'sort_order' => $data['sort_order'] ?? 0,
            'price_adjustment_type' => $data['price_adjustment_type'] ?? null,
            'price_adjustment' => $data['price_adjustment'] ?? 0,
        ]);

        return back()->with('status', 'Option value updated.');
    }

    public function destroyValue(Product $product, ProductOption $option, ProductOptionValue $value): RedirectResponse
    {
        $this->ensureValueBelongsToProductAndOption($product, $option, $value);

        $value->Products()->detach($product->id);
        $value->Variants()->detach();

        if (! $value->Products()->exists()) {
            $value->delete();
        }

        return back()->with('status', 'Option value removed.');
    }

    protected function ensureOptionBelongsToProduct(Product $product, ProductOption $option, bool $abortWhenMissing = true): void
    {
        $belongs = $product->OptionValues()
            ->where('product_option_values.product_option_id', $option->id)
            ->exists();

        if (! $belongs && $abortWhenMissing) {
            abort(404);
        }
    }

    protected function ensureValueBelongsToProductAndOption(Product $product, ProductOption $option, ProductOptionValue $value): void
    {
        if ((int) $value->product_option_id !== (int) $option->id) {
            abort(404);
        }

        $belongs = $value->Products()->where('products.id', $product->id)->exists();
        if (! $belongs) {
            abort(404);
        }
    }
}
