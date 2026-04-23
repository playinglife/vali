<?php

namespace App\Features\Admin\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_number' => $this->order_number,
            'email' => $this->email,
            'status' => $this->status,
            'currency' => $this->currency,
            'shipping_total' => (float) $this->shipping_total,
            'billing_address' => $this->billing_address,
            'shipping_address' => $this->shipping_address,
            'notes' => $this->notes,
            'placed_at' => $this->placed_at !== null
                ? Carbon::parse($this->placed_at)->format('Y-m-d H:i:s')
                : null,
            'paid_at' => $this->paid_at !== null
                ? Carbon::parse($this->paid_at)->format('Y-m-d H:i:s')
                : null,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];

        if ($this->relationLoaded('Items')) {
            $attributes['items'] = OrderItemResource::collection($this->Items)->resolve();
        }

        if ($this->relationLoaded('User') && $this->User !== null) {
            $attributes['user'] = [
                'id' => $this->User->id,
                'name' => $this->User->name,
                'email' => $this->User->email,
            ];
        }

        return $attributes;
    }
}
