<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Translation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'english',
        'romanian',
    ];

    /**
     * Resolved text for the given locale, with fallback to the other language when empty.
     */
    public function textForLocale(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale === 'ro') {
            if ($this->romanian !== null && $this->romanian !== '') {
                return $this->romanian;
            }
            if ($this->english !== null && $this->english !== '') {
                return $this->english;
            }

            return null;
        }

        if ($this->english !== null && $this->english !== '') {
            return $this->english;
        }
        if ($this->romanian !== null && $this->romanian !== '') {
            return $this->romanian;
        }

        return null;
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'description_id');
    }

    public function productsAsShortDescription(): HasMany
    {
        return $this->hasMany(Product::class, 'short_description_id');
    }

    public function productsAsDescription(): HasMany
    {
        return $this->hasMany(Product::class, 'description_id');
    }
}
