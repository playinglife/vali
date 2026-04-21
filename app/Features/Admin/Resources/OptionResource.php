<?php

namespace App\Features\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionResource extends JsonResource
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
            'name' => $this->name,
            'show_on_products' => (bool) $this->show_on_products,
            'type' => $this->type?->value ?? (string) $this->type,
            'sort_order' => (int) ($this->sort_order ?? 0),
            'values' => ValueResource::collection($this->whenLoaded('Values')),
        ];
    }
}
