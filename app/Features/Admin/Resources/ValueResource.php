<?php

namespace App\Features\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'option_id' => $this->product_option_id,
            'value' => $this->value,
            'icon' => $this->icon,
            'image' => $this->image,
            'price_adjustment_type' => $this->price_adjustment_type,
            'price_adjustment' => $this->price_adjustment !== null ? (float) $this->price_adjustment : null,
            'sort_order' => (int) ($this->sort_order ?? 0),
        ];
    }
}
