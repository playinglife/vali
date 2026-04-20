<?php

namespace App\Features\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;



class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (float) $this->price,
            'is_active' => $this->is_active ? 'Yes' : 'No',
            'variants_count' => $this->Variants->count(),
            'discount_type' => $this->discount_type,
            'discount' => (float) $this->discount,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
        return $attributes;
    }
}
