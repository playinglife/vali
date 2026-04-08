<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        $product->Variants()->create([
            'sku' => $product->sku,
            'price' => null,
            'discount_type' => null,
            'discount' => null,
            'stock_quantity' => 0,
            'weight' => $product->weight,
            'is_active' => true,
        ]);
    }
}
