<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'discount_type',
        'discount',
        'stock_quantity',
        'weight',
        'barcode',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'is_active' => 'boolean',
            'weight' => 'decimal:3',
        ];
    }

    /**
     * False when `stock_quantity` is null (inventory not enforced).
     */
    public function tracksStock(): bool
    {
        return $this->stock_quantity !== null;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_option_values',
            'product_variant_id',
            'product_option_value_id'
        )->withTimestamps();
    }
}
