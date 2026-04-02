<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
        ]);

        $product = Product::query()
            ->with(['variants.priceBrackets', 'priceBrackets'])
            ->findOrFail($validated['product_id']);
        abort_unless($product->is_active, 404);

        if (! $product->isInStock()) {
            return redirect()->back();
        }

        $qty = (int) ($validated['quantity'] ?? 1);
        $variantId = $validated['product_variant_id'] ?? null;

        $variant = null;
        if ($product->variants->isNotEmpty()) {
            if ($variantId === null) {
                return redirect()->back()->withInput();
            }
            $variant = $product->variants->firstWhere('id', $variantId);
            if ($variant === null || ! $variant->is_active) {
                return redirect()->back()->withInput();
            }
            if ($variant->tracksStock() && (int) $variant->stock_quantity <= 0) {
                return redirect()->back();
            }
            $unitPrice = $variant->unitPriceForQuantity($qty);
            $discountPerUnit = $variant->discountPerUnitForQuantity($qty);
        } else {
            if ($variantId !== null) {
                return redirect()->back()->withInput();
            }
            $unitPrice = $product->unitPriceForQuantity($qty);
            $discountPerUnit = $product->discountPerUnitForQuantity($qty);
        }

        $lines = session()->get('cart', []);
        $merged = false;
        foreach ($lines as $i => $line) {
            if ((int) $line['product_id'] === $product->id
                && (int) ($line['product_variant_id'] ?? 0) === (int) ($variant?->id ?? 0)) {
                $newQty = (int) $line['quantity'] + $qty;
                if ($variant instanceof ProductVariant) {
                    $lines[$i]['quantity'] = $newQty;
                    $lines[$i]['unit_price'] = $variant->unitPriceForQuantity($newQty);
                    $lines[$i]['discount_per_unit'] = $variant->discountPerUnitForQuantity($newQty);
                } else {
                    $lines[$i]['quantity'] = $newQty;
                    $lines[$i]['unit_price'] = $product->unitPriceForQuantity($newQty);
                    $lines[$i]['discount_per_unit'] = $product->discountPerUnitForQuantity($newQty);
                }
                $merged = true;
                break;
            }
        }

        if (! $merged) {
            $lines[] = [
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'discount_per_unit' => $discountPerUnit,
            ];
        }

        session()->put('cart', array_values($lines));

        $lineTotal = round($unitPrice * $qty, 2);

        session()->flash('cart_added', [
            'product_name' => $product->name,
            'quantity' => $qty,
            'line_total' => $lineTotal,
            'currency' => __('components.product.currency'),
        ]);

        return redirect()->back();
    }
}
