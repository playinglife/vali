<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'show_on_products',
        'image',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'show_on_products' => 'boolean',
        ];
    }

    public function Values(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class)->orderBy('sort_order');
    }
}
