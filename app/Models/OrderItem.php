<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'sku',
        'quantity',
        'price',
        'discount_type',
        'discount',
        'currency',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
        ];
    }

    public function Order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function Variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
