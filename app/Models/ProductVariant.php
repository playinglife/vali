<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'description_id',
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

    public function descriptionTranslation(): BelongsTo
    {
        return $this->belongsTo(Translation::class, 'description_id');
    }

    /**
     * Variant description text for the given app locale (falls back to the other language when empty).
     */
    public function localizedDescription(?string $locale = null): ?string
    {
        return $this->descriptionTranslation?->textForLocale($locale);
    }

    public function priceBrackets(): HasMany
    {
        return $this->hasMany(PriceBracket::class)
            ->orderBy('sort_order')
            ->orderBy('start_quantity');
    }

    /**
     * List / “was” price before discount, derived from variant price and discount fields.
     */
    public function listPriceBeforeDiscount(): ?float
    {
        $product = $this->product;
        $price = (float) ($this->price ?? $product->price);
        $type = $this->discount_type;
        if ($type === null || $this->discount === null) {
            return null;
        }

        $discount = (float) $this->discount;
        if ($discount <= 0) {
            return null;
        }

        if ($type === 'percentage') {
            if ($discount >= 100) {
                return null;
            }

            return round($price / (1 - $discount / 100), 2);
        }

        if ($type === 'fix') {
            return round($price + $discount, 2);
        }

        return null;
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_option_values',
            'product_variant_id',
            'product_option_value_id'
        )->withPivot('with_image')
            ->withTimestamps();
    }

    /**
     * Public URL for an image on the public disk under product/variants/{id}.{ext}, or null if none.
     */
    public function storageImageUrl(): ?string
    {
        $disk = Storage::disk('public');

        foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
            $path = 'product/variants/'.$this->id.'.'.$ext;
            if ($disk->exists($path)) {
                return asset('storage/'.$path);
            }
        }

        return null;
    }

    /**
     * Image URL for this variant, or the product generic placeholder when no file exists.
     */
    public function displayImageUrl(): string
    {
        return $this->storageImageUrl() ?? Product::genericProductImageUrl();
    }

    /**
     * Unit sale price for the given order quantity (volume brackets when present).
     */
    public function unitPriceForQuantity(int $quantity): float
    {
        $product = $this->product;
        $brackets = $this->priceBrackets->isNotEmpty()
            ? $this->priceBrackets
            : $product->priceBrackets;

        foreach ($brackets as $b) {
            $min = (int) $b->start_quantity;
            if ($quantity < $min) {
                continue;
            }
            $max = $b->end_quantity !== null ? (int) $b->end_quantity : null;
            if ($max !== null && $quantity > $max) {
                continue;
            }

            return round((float) $b->price, 2);
        }

        return round((float) ($this->price ?? $product->price), 2);
    }

    /**
     * Per-unit discount amount (list minus sale) when a compare-at list price exists.
     */
    public function discountPerUnitForQuantity(int $quantity): float
    {
        $list = $this->listPriceBeforeDiscount();
        if ($list === null) {
            return 0.0;
        }
        $unit = $this->unitPriceForQuantity($quantity);
        $diff = round((float) $list - $unit, 2);

        return $diff > 0 ? $diff : 0.0;
    }
}
