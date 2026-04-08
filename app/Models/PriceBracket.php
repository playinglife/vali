<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceBracket extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'start_quantity',
        'end_quantity',
        'price',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ProductVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
