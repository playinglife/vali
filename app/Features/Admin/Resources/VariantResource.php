<?php

namespace App\Features\Admin\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class VariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $values = $this->relationLoaded('Values')
            ? $this->Values
            : collect();

        $attributes = [
            'uniqueId' => 'variant-' . $this->id,
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'price' => $this->price !== null ? (float) $this->price : null,
            'stock_quantity' => $this->stock_quantity,
            'discount_type' => $this->discount_type?->value ?? (string) $this->discount_type,
            'discount' => $this->discount !== null ? (float) $this->discount : null,
            'weight' => $this->weight !== null ? (float) $this->weight : null,
            'barcode' => $this->barcode,
            'is_active' => (bool) $this->is_active,
            'image' => $this->image,
            'product_variant_images' => $this->whenLoaded(
                'VariantImages',
                fn () => $this->VariantImages
                    ->map(fn ($variantImage) => $variantImage->image)
                    ->filter()
                    ->values(),
                []
            ),
            'values_label' => $values
                ->map(fn ($value) => $value->value)
                ->filter()
                ->implode(', '),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];

        if ($this->relationLoaded('Values')) {
            $attributes['options'] = OptionResource::collection($this->groupOptionsFromValues($values));
        }

        return $attributes;
    }

    /**
     * Build option resources from variant-linked option values.
     *
     * @param Collection<int, mixed> $values
     * @return Collection<int, mixed>
     */
    protected function groupOptionsFromValues(Collection $values): Collection
    {
        return $values
            ->filter(fn ($value) => $value->Option !== null)
            ->groupBy('product_option_id')
            ->map(function (Collection $group) {
                $option = clone $group->first()->Option;
                $option->setRelation('Values', $group->values());
                return $option;
            })
            ->values();
    }
}
