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

        $product = Product::find($validated['product_id']);
        $variant = ProductVariant::find($validated['product_variant_id']);
        if ($product === null) {
            return redirect()->back()->withErrors(['product_id' => 'Product not found']);
        }
        if ($variant === null) {
            return redirect()->back()->withErrors(['product_variant_id' => 'Variant not found']);
        }
        if (! $product->is_active) {
            return redirect()->back()->withErrors(['product_id' => 'Product is not active']);
        }
        if (! $product->isInStock()) {
            return redirect()->back()->withErrors(['product_id' => 'Product is not in stock']);
        }

        $qty = (int) ($validated['quantity'] ?? 1);


        // Add to session cart
        $lines = session()->get('cart', []);
        $nextId = 1;
        foreach (session()->get('cart', []) as $line) {
            if ((int) ($line['id'] ?? 0) > $nextId) {
                $nextId = (int) ($line['id'] ?? 0);
            }
        }
        $nextId++;
        $lines[] = [
            'id' => $nextId,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'quantity' => $qty,
        ];
        session()->put('cart', array_values($lines));

        
        // Flash message
        $lineTotal = round($variant->price * $qty, 2);
        session()->flash('cart_added', [
            'product_name' => $product->name,
            'quantity' => $qty,
            'line_total' => $lineTotal,
            'currency' => __('components.product.currency'),
        ]);

        return redirect()->back();
    }

    public function remove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
        ]);
        $lines = session()->get('cart', []);
        $lines = array_values(array_filter($lines, function (array $line) use ($validated): bool {
            return (int) ($line['id'] ?? 0) !== (int) $validated['id'];
        }));
        session()->put('cart', array_values($lines));
        return redirect()->back();
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return redirect()->back();
    }
}
