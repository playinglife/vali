<?php

namespace App\Models;

use App\Enums\ProductOptionType;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'show_on_products',
        'type',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'show_on_products' => 'boolean',
            'type' => ProductOptionType::class,
        ];
    }

    public function Values(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class)->orderBy('sort_order');
    }
}
