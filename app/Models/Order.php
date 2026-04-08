<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'email',
        'status',
        'currency',
        'subtotal',
        'tax_total',
        'shipping_total',
        'discount_total',
        'grand_total',
        'billing_address',
        'shipping_address',
        'notes',
        'placed_at',
        'paid_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'billing_address' => 'array',
            'shipping_address' => 'array',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function Items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
