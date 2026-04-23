<?php

namespace App\Features\Admin\Controllers;

use App\Features\Admin\Resources\OrderItemResource;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderItemController extends Controller
{
    public function index(Order $order): JsonResponse
    {
        $items = $order->Items()
            ->with(['Product', 'Variant'])
            ->orderBy('id')
            ->get();

        return response()->json(OrderItemResource::collection($items)->resolve());
    }

    public function store(Request $request, Order $order): JsonResponse
    {
        $data = $this->validated($request, $order);
        $item = $order->Items()->create($data);
        $item->load(['Product', 'Variant']);

        return response()->json(OrderItemResource::make($item)->resolve(), 201);
    }

    public function update(Request $request, Order $order, OrderItem $item): JsonResponse
    {
        $this->ensureItemBelongsToOrder($order, $item);
        $data = $this->validated($request, $order, $item->id);
        $item->update($data);
        $item->load(['Product', 'Variant']);

        return response()->json(OrderItemResource::make($item)->resolve());
    }

    public function destroy(Order $order, string $orderItems): JsonResponse
    {
        $ids = collect(explode(',', $orderItems))
            ->map(static fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();
        if ($ids->isEmpty()) {
            return response()->json([], 200);
        }
        $matchingCount = OrderItem::query()
            ->where('order_id', $order->id)
            ->whereIn('id', $ids->all())
            ->count();
        if ($matchingCount !== $ids->count()) {
            abort(404);
        }
        OrderItem::query()
            ->where('order_id', $order->id)
            ->whereIn('id', $ids->all())
            ->delete();

        return response()->json([], 200);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, Order $order, ?int $itemId = null): array
    {
        $skuUnique = Rule::unique('order_items', 'sku')->where('order_id', $order->id);
        if ($itemId !== null) {
            $skuUnique = $skuUnique->ignore($itemId);
        }

        $data = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'sku' => [
                'required',
                'string',
                'max:255',
                $skuUnique,
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'string', Rule::in(['fixed', 'percentage'])],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
        ]);

        return $data;
    }

    protected function ensureItemBelongsToOrder(Order $order, OrderItem $item): void
    {
        if ((int) $item->order_id !== (int) $order->id) {
            abort(404);
        }
    }
}
