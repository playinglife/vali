<?php

namespace App\Support;

use App\Enums\DiscountType;
use App\Models\ProductVariant;

class VariantPricing
{
    /**
     * Build normalized pricing payload for one variant and quantity.
     *
     * @return array<string, mixed>|null
     */
    public static function forVariantId(int $variantId, int $quantity = 1): ?array
    {
        $variant = ProductVariant::query()
            ->with(['Product', 'PriceBrackets', 'Product.PriceBrackets'])
            ->find($variantId);
        if ($variant === null || $variant->Product === null) {
            return null;
        }
        $qty = max(1, $quantity);
        $unitPrice = $variant->unitPriceForQuantity($qty);
        $unitDiscountAmount = $variant->discountPerUnitForQuantity($qty);
        $data = [
            'product_id' => (int) $variant->product_id,
            'product_name' => (string) $variant->Product->name,
            'variant_id' => (int) $variant->id,
            'sku' => (string) $variant->sku,
            'quantity' => $qty,
            'price' => $unitPrice,
            'discount' => $variant->discount,
            'discount_type' => $variant->discount_type?->value ?? (string) $variant->discount_type,
            'discount_price' => $variant->discount_type === DiscountType::Percentage ? $unitPrice * $variant->discount / 100 : $unitPrice - $variant->discount,
            'currency' => __('components.product.currency'),
        ];
        $data['total'] = round($data['discount_price'] * $qty, 2);
        if ($data['discount_price'] < 0) {
            $data['discount_price'] = 0;
        }
        if ($data['total'] < 0) {
            $data['total'] = 0;
        }
        return $data;
    }
}
