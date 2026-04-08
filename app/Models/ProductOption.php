<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'name',
        'sort_order',
    ];

    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function Values(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class)->orderBy('sort_order');
    }
}
