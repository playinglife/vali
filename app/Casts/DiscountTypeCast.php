<?php

namespace App\Casts;

use App\Enums\DiscountType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Maps legacy {@see DiscountType} storage value {@code fix} to {@see DiscountType::Fixed}.
 */
class DiscountTypeCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?DiscountType
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = (string) $value;

        if ($raw === 'fix') {
            return DiscountType::Fixed;
        }

        return DiscountType::tryFrom($raw);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DiscountType) {
            return $value->value;
        }

        if ((string) $value === 'fix') {
            return DiscountType::Fixed->value;
        }

        return DiscountType::tryFrom((string) $value)?->value;
    }
}
