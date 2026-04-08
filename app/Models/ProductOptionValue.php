<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class ProductOptionValue extends BaseModel
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

    protected function image(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->storageImageUrl());
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

    public function Option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function Variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_option_values',
            'product_option_value_id',
            'product_variant_id'
        )->withTimestamps();
    }

    public function storageImageUrl(): ?string
    {
        $disk = Storage::disk('public');

        foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
            $path = 'product/variants/'.$this->id.'.'.$ext;
            if ($disk->exists($path)) {
                return asset('storage/'.$path);
            }
        }

        return asset('images/generic.png');
    }
}
