<?php

namespace App\Models;

use App\Casts\DiscountTypeCast;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ProductVariant extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $appends = ['image'];

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
        'image',
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
            'discount_type' => DiscountTypeCast::class,
            'discount' => 'decimal:2',
            'is_active' => 'boolean',
            'weight' => 'decimal:3',
        ];
    }

    protected function image(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->storageImageUrl());
    }

    /**
     * False when `stock_quantity` is null (inventory not enforced).
     */
    public function tracksStock(): bool
    {
        return $this->stock_quantity !== null;
    }

    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function DescriptionTranslation(): BelongsTo
    {
        return $this->belongsTo(Translation::class, 'description_id');
    }

    /**
     * Variant description text for the given app locale (falls back to the other language when empty).
     */
    public function localizedDescription(?string $locale = null): ?string
    {
        return $this->DescriptionTranslation?->textForLocale($locale);
    }

    public function PriceBrackets(): HasMany
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
        $product = $this->Product;
        $price = (float) ($this->price ?? $product->price);
        $type = $this->discount_type;
        if ($type === null || $this->discount === null) {
            return null;
        }

        $discount = (float) $this->discount;
        if ($discount <= 0) {
            return null;
        }

        if ($type === DiscountType::Percentage) {
            if ($discount >= 100) {
                return null;
            }

            return round($price / (1 - $discount / 100), 2);
        }

        if ($type === DiscountType::Fixed) {
            return round($price + $discount, 2);
        }

        return null;
    }

    public function Values(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_option_values',
            'product_variant_id',
            'product_option_value_id',
        )->withTimestamps();
    }

    public function storageImageUrl(): ?string
    {
        $path = $this->attributes['image'] ?? null;
        if (is_string($path) && $path !== '') {
            return asset('storage/'.$path);
        }

        $disk = Storage::disk('public');

        foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
            $legacyPath = 'product/variants/'.$this->id.'.'.$ext;
            if ($disk->exists($legacyPath)) {
                return asset('storage/'.$legacyPath);
            }
        }

        return asset('images/generic.png');
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
        $product = $this->Product;
        $brackets = $this->PriceBrackets->isNotEmpty()
            ? $this->PriceBrackets
            : $product->PriceBrackets;

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
