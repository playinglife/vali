<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy([ProductObserver::class])]
class Product extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'sku',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'discount_type',
        'discount',
        'cost',
        'is_active',
        'is_featured',
        'weight',
        'meta_title',
        'meta_description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'cost' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'weight' => 'decimal:3',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * The first variant created for the product (auto-created with the product, same SKU as the product).
     */
    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->oldest('id');
    }

    /**
     * Total inventory across all variants.
     */
    public function resolvedStockQuantity(): int
    {
        return (int) ($this->variants()->sum('stock_quantity') ?? 0);
    }

    /**
     * Whether at least one variant is sellable from an inventory perspective.
     * Variants with null `stock_quantity` are not tracked and count as in stock.
     */
    public function isInStock(): bool
    {
        return $this->variants()
            ->where(function ($query) {
                $query->whereNull('stock_quantity')
                    ->orWhere('stock_quantity', '>', 0);
            })
            ->exists();
    }

    /**
     * Public URL for the first variant image found on the public disk under product/variants/{id}.{ext},
     * or {@see static::genericProductImageUrl()} when none exists.
     */
    public function firstVariantStorageImageUrl(): string
    {
        $variants = $this->relationLoaded('variants')
            ? $this->variants->sortBy('id')->values()
            : $this->variants()->orderBy('id')->get();

        foreach ($variants as $variant) {
            $url = $variant->storageImageUrl();
            if ($url !== null) {
                return $url;
            }
        }

        return static::genericProductImageUrl();
    }

    /**
     * Fallback image URL when no variant file exists on disk.
     */
    public static function genericProductImageUrl(): string
    {
        return asset('images/generic.png');
    }

    /**
     * List / “was” price before discount, derived from {@see $price} and discount fields.
     * Returns null when there is no valid discount configuration.
     */
    public function listPriceBeforeDiscount(): ?float
    {
        $price = (float) $this->price;
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
}
