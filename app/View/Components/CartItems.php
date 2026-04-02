<?php

namespace App\View\Components;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class CartItems extends Component
{
    /**
     * @var list<array{product: \App\Models\Product, rows: list<array{image_url: string, label: string, quantity: int, unit_price: float, line_total: float}>}>
     */
    public array $groups = [];

    public float $grandTotal = 0.0;

    public bool $cartIsEmpty = true;

    public string $currency;

    public function __construct()
    {
        $this->currency = __('components.product.currency');
        $lines = session('cart', []);

        if ($lines === []) {
            return;
        }

        /** @var list<array{product_id: int, product_variant_id?: int|null, quantity: int, unit_price: float|int|string, discount_per_unit?: float|int|string}> $lines */
        $productIds = collect($lines)->pluck('product_id')->unique()->filter()->map(fn ($id) => (int) $id)->values()->all();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->with(['variants.optionValues.option'])
            ->get()
            ->keyBy('id');

        $groupOrder = [];
        $linesByProduct = [];
        foreach ($lines as $line) {
            $pid = (int) ($line['product_id'] ?? 0);
            if ($pid === 0) {
                continue;
            }
            if (! isset($linesByProduct[$pid])) {
                $linesByProduct[$pid] = [];
                $groupOrder[] = $pid;
            }
            $linesByProduct[$pid][] = $line;
        }

        $grandTotal = 0.0;
        $groups = [];

        foreach ($groupOrder as $pid) {
            $product = $products->get($pid);
            if ($product === null) {
                continue;
            }
            $rows = [];
            foreach ($linesByProduct[$pid] as $line) {
                $qty = (int) ($line['quantity'] ?? 0);
                $unitPrice = (float) ($line['unit_price'] ?? 0);
                $lineTotal = round($unitPrice * $qty, 2);
                $grandTotal += $lineTotal;

                $variantId = isset($line['product_variant_id']) ? (int) $line['product_variant_id'] : null;
                $variant = $variantId !== null && $variantId !== 0
                    ? $product->variants->firstWhere('id', $variantId)
                    : null;

                if ($variant instanceof ProductVariant) {
                    $imageUrl = $variant->displayImageUrl();
                    $label = $this->variantLabel($variant);
                } else {
                    $imageUrl = $product->firstVariantStorageImageUrl();
                    if ($product->variants->isEmpty()) {
                        $label = __('pages.cart.default_line');
                    } elseif ($variantId !== null && $variantId !== 0) {
                        $label = __('pages.cart.variant_fallback', ['id' => $variantId]);
                    } else {
                        $label = __('pages.cart.default_line');
                    }
                }

                $rows[] = [
                    'image_url' => $imageUrl,
                    'label' => $label,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            if ($rows !== []) {
                $groups[] = [
                    'product' => $product,
                    'rows' => $rows,
                ];
            }
        }

        $this->groups = $groups;
        $this->grandTotal = round($grandTotal, 2);
        $this->cartIsEmpty = $groups === [];
    }

    private function variantLabel(ProductVariant $variant): string
    {
        if ($variant->relationLoaded('optionValues') && $variant->optionValues->isNotEmpty()) {
            /** @var Collection<int, \App\Models\ProductOptionValue> $sorted */
            $sorted = $variant->optionValues->sortBy(function ($ov) {
                $optOrder = (int) ($ov->option?->sort_order ?? 0);
                $valOrder = (int) ($ov->sort_order ?? 0);

                return sprintf('%04d-%04d-%s', $optOrder, $valOrder, $ov->value ?? '');
            })->values();

            return $sorted->map(function ($ov) {
                $name = $ov->option?->name ?? '';

                return trim($name) !== '' ? $name.': '.$ov->value : (string) $ov->value;
            })->implode(', ');
        }

        $sku = trim((string) ($variant->sku ?? ''));

        return $sku !== '' ? $sku : __('pages.cart.variant_fallback', ['id' => $variant->id]);
    }

    public function render(): View
    {
        return view('components.cart-items');
    }
}
