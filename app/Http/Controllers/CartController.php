<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        abort_unless($product->is_active, 404);

        if (! $product->isInStock()) {
            return redirect()->back();
        }

        $qty = (int) ($validated['quantity'] ?? 1);
        $cart = session()->get('cart', []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $qty;
        session()->put('cart', $cart);

        return redirect()->back();
    }
}
