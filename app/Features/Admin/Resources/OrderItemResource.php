<?php

namespace App\Features\Admin\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'sku' => $this->sku,
            'quantity' => (int) $this->quantity,
            'price' => (float) $this->price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount !== null ? (float) $this->discount : null,
            'currency' => $this->currency,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];

        if ($this->relationLoaded('Product') && $this->Product !== null) {
            $attributes['product'] = [
                'id' => $this->Product->id,
                'name' => $this->Product->name,
                'sku' => $this->Product->sku,
            ];
        }

        if ($this->relationLoaded('Variant') && $this->Variant !== null) {
            $attributes['variant'] = [
                'id' => $this->Variant->id,
                'sku' => $this->Variant->sku,
            ];
        }

        return $attributes;
    }
}
