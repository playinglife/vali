<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Translation extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'english',
        'romanian',
    ];

    public function ProductVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'description_id');
    }

    public function ProductsAsShortDescription(): HasMany
    {
        return $this->hasMany(Product::class, 'short_description_id');
    }

    public function ProductsAsDescription(): HasMany
    {
        return $this->hasMany(Product::class, 'description_id');
    }

    public function ProductsAsMetaTitle(): HasMany
    {
        return $this->hasMany(Product::class, 'meta_title_id');
    }

    public function ProductsAsMetaDescription(): HasMany
    {
        return $this->hasMany(Product::class, 'meta_description_id');
    }
}
