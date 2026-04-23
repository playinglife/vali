<?php

namespace App\Features\Admin\Controllers;

use App\Features\Admin\Resources\OrderResource;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->with(['Items'])
            ->orderByDesc('id')
            ->get();

        return response()->json(OrderResource::collection($orders)->resolve());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        if (empty($data['order_number'])) {
            $data['order_number'] = 'ORD-'.strtoupper(Str::random(10));
        }
        $data['currency'] = $data['currency'] ?? 'RON';
        $data['status'] = $data['status'] ?? 'pending';
        $data['shipping_total'] = $data['shipping_total'] ?? 0;
        $order = Order::query()->create($data);
        $order->load(['Items']);

        return response()->json(OrderResource::make($order)->resolve(), 201);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $data = $this->validated($request, $order->id);
        $order->update($data);
        $order->load(['Items']);

        return response()->json(OrderResource::make($order)->resolve());
    }

    public function destroy(string $orders): JsonResponse
    {
        $ids = collect(explode(',', $orders))
            ->map(static fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();
        if ($ids->isEmpty()) {
            return response()->json([], 200);
        }
        Order::query()->whereIn('id', $ids->all())->delete();

        return response()->json([], 200);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, ?int $orderId = null): array
    {
        $orderNumberRule = Rule::unique('orders', 'order_number');
        if ($orderId !== null) {
            $orderNumberRule = $orderNumberRule->ignore($orderId);
        }

        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'order_number' => ['nullable', 'string', 'max:255', $orderNumberRule],
            'email' => ['required', 'string', 'email', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:3'],
            'shipping_total' => ['nullable', 'numeric', 'min:0'],
            'billing_address' => ['nullable', 'array'],
            'shipping_address' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'placed_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
        ]);

        return $data;
    }
}
