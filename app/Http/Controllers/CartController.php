<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\VariantPricing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class CartController extends Controller
{
    public function showCheckout(): View|RedirectResponse
    {
        return $this->redirectIfCartEmpty() ?? view('pages.checkout');
    }

    protected function redirectIfCartEmpty(): ?RedirectResponse
    {
        if (session()->get('cart', []) === []) {
            return redirect()->route('cart')->withNotify('info', __('pages.cart.no_items_flash'));
        }

        return null;
    }

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
        //session()->put('cart', []);
        $lines = session()->get('cart', []);
        $nextId = 1;
        foreach (session()->get('cart', []) as $line) {
            if ((int) ($line['id'] ?? 0) > $nextId) {
                $nextId = (int) ($line['id'] ?? 0);
            }
        }
        $nextId++;
        $pricing = VariantPricing::forVariantId((int) $variant->id, $qty);
        if ($pricing === null) {
            return redirect()->back()->withErrors(['product_variant_id' => 'Unable to calculate variant price']);
        }
        $newItem = array_merge(['id' => $nextId], $pricing);
        $lines[] = $newItem;
        session()->put('cart', array_values($lines));
        
        // Flash message
        session()->flash('cart_added', $newItem);

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

    public function confirmOrder(Request $request): RedirectResponse
    {
        if (($redirect = $this->redirectIfCartEmpty()) !== null) {
            return $redirect;
        }

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'email.required' => __('pages.checkout.errors.email_required'),
            'email.email' => __('pages.checkout.errors.email_invalid'),
            'email.max' => __('pages.checkout.errors.email_max'),
            'name.required' => __('pages.checkout.errors.name_required'),
            'name.max' => __('pages.checkout.errors.name_max'),
            'company.max' => __('pages.checkout.errors.company_max'),
            'country.required' => __('pages.checkout.errors.country_required'),
            'country.max' => __('pages.checkout.errors.country_max'),
            'city.required' => __('pages.checkout.errors.city_required'),
            'city.max' => __('pages.checkout.errors.city_max'),
            'phone.required' => __('pages.checkout.errors.phone_required'),
            'phone.max' => __('pages.checkout.errors.phone_max'),
            'notes.max' => __('pages.checkout.errors.notes_max'),
        ]);


        $order = Order::create([
            'email' => $request->email,
            'name' => $request->name,
            'company' => $request->company,
            'country' => $request->country,
            'city' => $request->city,
            'phone' => $request->phone,
            'notes' => $request->notes,
        ]);
        foreach (session()->get('cart', []) as $line) {
            $order->items()->create([
                'product_id' => $line['product_id'] ?? $line['id'] ?? null,
                'variant_id' => $line['variant_id'] ?? $line['product_variant_id'] ?? null,
                'quantity' => $line['quantity'],
                'price' => $line['price'],
                'discount' => $line['discount'] ?? null,
                'discount_type' => $line['discount_type'] ?? null,
                'currency' => $line['currency'] ?? 'RON',
                'sku' => $line['sku'],
            ]);
        }
        $order->save();

        session()->forget('cart');

        return redirect()->route('thankyou')->with('order_confirmed', true);
    }
}
