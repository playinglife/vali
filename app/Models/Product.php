<?php

namespace App\Models;

use App\Casts\DiscountTypeCast;
use App\Enums\DiscountType;
use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

#[ObservedBy([ProductObserver::class])]
class Product extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'sku',
        'name',
        'slug',
        'short_description_id',
        'description_id',
        'price',
        'discount_type',
        'discount',
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
            'discount_type' => DiscountTypeCast::class,
            'discount' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'weight' => 'decimal:3',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function ShortDescriptionTranslation(): BelongsTo
    {
        return $this->belongsTo(Translation::class, 'short_description_id');
    }

    public function DescriptionTranslation(): BelongsTo
    {
        return $this->belongsTo(Translation::class, 'description_id');
    }

    public function localizedShortDescription(?string $locale = null): ?string
    {
        return $this->ShortDescriptionTranslation?->textForLocale($locale);
    }

    /**
     * Long product description (body copy) for the given locale.
     */
    public function localizedDescription(?string $locale = null): ?string
    {
        return $this->DescriptionTranslation?->textForLocale($locale);
    }

    public function Categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function OptionValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionValue::class, 'product_product_option_value')
            ->withTimestamps()
            ->orderBy('product_option_values.sort_order');
    }

    /**
     * Product option groups built from assigned option values.
     *
     * @return Collection<int, object{id:int,name:string,show_on_products:bool,type:string,sort_order:int,Values:Collection<int, ProductOptionValue>}>
     */
    public function groupedOptions(): Collection
    {
        $values = $this->relationLoaded('OptionValues')
            ? $this->OptionValues
            : $this->OptionValues()->with('Option')->get();

        return $values
            ->filter(fn (ProductOptionValue $value) => $value->Option !== null)
            ->groupBy('product_option_id')
            ->map(function (Collection $group): object {
                /** @var ProductOptionValue $first */
                $first = $group->first();
                $option = $first->Option;

                return (object) [
                    'id' => (int) $option->id,
                    'name' => (string) $option->name,
                    'show_on_products' => (bool) $option->show_on_products,
                    'type' => $option->type?->value ?? 'text',
                    'sort_order' => (int) ($option->sort_order ?? 0),
                    'Values' => $group
                        ->sortBy('sort_order')
                        ->values(),
                ];
            })
            ->sortBy('sort_order')
            ->values();
    }

    public function Variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Volume pricing tiers for the product when no variant-specific brackets exist.
     */
    public function PriceBrackets(): HasMany
    {
        return $this->hasMany(PriceBracket::class)
            ->whereNull('product_variant_id')
            ->orderBy('sort_order')
            ->orderBy('start_quantity');
    }

    /**
     * The first variant created for the product (auto-created with the product, same SKU as the product).
     */
    public function DefaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->oldest('id');
    }

    /**
     * Gallery images for the product, ordered for display.
     */
    public function ProductImages(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Total inventory across all variants.
     */
    public function resolvedStockQuantity(): int
    {
        return (int) ($this->Variants()->sum('stock_quantity') ?? 0);
    }

    /**
     * Whether at least one variant is sellable from an inventory perspective.
     * Variants with null `stock_quantity` are not tracked and count as in stock.
     */
    public function isInStock(): bool
    {
        return $this->Variants()
            ->where(function ($query) {
                $query->whereNull('stock_quantity')
                    ->orWhere('stock_quantity', '>', 0);
            })
            ->exists();
    }

    /**
     * Public URL for the first variant gallery image (see {@see ProductVariant::VariantImages()}),
     * or {@see static::genericProductImageUrl()} when none exists.
     */
    public function firstVariantStorageImageUrl(): string
    {
        $variants = $this->relationLoaded('Variants')
            ? $this->Variants->sortBy('id')->values()
            : $this->Variants()->orderBy('id')->get();

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

    /**
     * Unit sale price for the given order quantity (volume brackets on the product when no variant-specific tiers).
     */
    public function unitPriceForQuantity(int $quantity): float
    {
        foreach ($this->PriceBrackets as $b) {
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

        return round((float) $this->price, 2);
    }

    /**
     * Per-unit discount amount when a compare-at list price exists.
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
