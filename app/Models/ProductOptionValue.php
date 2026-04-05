<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductOptionValue extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_option_id',
        'value',
        'price_adjustment_type',
        'price_adjustment',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_adjustment' => 'decimal:2',
        ];
    }

    /**
     * Monetary amount added to the base price for this option value.
     * For `percentage` type, `$basePrice` is the product base price.
     */
    public function adjustmentAmountForBasePrice(float $basePrice): float
    {
        $type = $this->price_adjustment_type;
        $amount = (float) ($this->price_adjustment ?? 0);

        if ($type === null) {
            return 0.0;
        }

        if ($type === 'fix') {
            return round($amount, 2);
        }

        if ($type === 'percentage') {
            if ($amount <= 0) {
                return 0.0;
            }

            return round($basePrice * ($amount / 100), 2);
        }

        return 0.0;
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_option_values',
            'product_option_value_id',
            'product_variant_id'
        )->withPivot('with_image')
            ->withTimestamps();
    }
}
