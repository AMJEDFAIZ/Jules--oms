<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Currency;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Lock Currency
            $currency = Currency::findOrFail($data['currency_id']);
            $exchangeRate = $currency->exchange_rate;

            // 2. Generate Tracking Code
            $trackingCode = 'YMN-' . date('Y') . '-' . strtoupper(Str::random(6));

            // 3. Create Order
            $order = Order::create([
                'tracking_code' => $trackingCode,
                'store_id' => $data['store_id'],
                'customer_id' => $data['customer_id'],
                'sales_user_id' => auth()->id() ?? null,
                'currency_id' => $currency->id,
                'exchange_rate_locked' => $exchangeRate,
                'order_status' => $data['is_reservation'] ?? false ? 'RESERVED' : 'NEW',
                'payment_status' => 'UNPAID',
                'accounting_status' => 'PENDING',
                'delivery_area_id' => $data['delivery_area_id'] ?? null,
                'delivery_type_id' => $data['delivery_type_id'] ?? null,
                'delivery_fee' => $data['delivery_fee'] ?? 0,
                'subtotal' => $data['subtotal'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'total' => $data['total'] ?? 0,
                'reservation_expires_at' => $data['reservation_expires_at'] ?? null,
            ]);

            // 4. Create Items & Reserve Stock
            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                    'is_attachment' => $item['is_attachment'] ?? false,
                    'attachment_note' => $item['attachment_note'] ?? null,
                ]);

                if (!($item['is_attachment'] ?? false) && isset($item['product_id'])) {
                    // Reserve inventory
                    $inventory = Inventory::firstOrCreate(
                        ['store_id' => $data['store_id'], 'product_id' => $item['product_id'], 'product_variant_id' => $item['product_variant_id'] ?? null]
                    );
                    $inventory->increment('reserved_stock', $item['quantity']);
                }
            }

            // 5. Create Status Log
            $order->statusLogs()->create([
                'user_id' => auth()->id() ?? null,
                'status_type' => 'order',
                'new_status' => $order->order_status,
                'notes' => 'Order created',
            ]);

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $newStatus, string $note = null)
    {
        return DB::transaction(function () use ($order, $newStatus, $note) {
            $oldStatus = $order->order_status;

            // Handle specific lifecycle timestamps
            $updates = ['order_status' => $newStatus];
            if ($newStatus === 'READY') $updates['prepared_at'] = now();
            if ($newStatus === 'WITH_DRIVER') $updates['handed_to_driver_at'] = now();
            if ($newStatus === 'OUT_FOR_DELIVERY') $updates['out_for_delivery_at'] = now();
            if ($newStatus === 'DELIVERED') $updates['delivered_at'] = now();
            if ($newStatus === 'RETURNED') $updates['returned_at'] = now();
            if ($newStatus === 'CANCELLED') {
                // Free reserved stock
                $this->freeReservedStock($order);
            }

            $order->update($updates);

            $order->statusLogs()->create([
                'user_id' => auth()->id() ?? null,
                'status_type' => 'order',
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $note,
            ]);

            return $order;
        });
    }

    private function freeReservedStock(Order $order)
    {
        foreach ($order->items as $item) {
            if (!$item->is_attachment && $item->product_id) {
                $inventory = Inventory::where([
                    'store_id' => $order->store_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                ])->first();

                if ($inventory) {
                    $inventory->decrement('reserved_stock', $item->quantity);
                }
            }
        }
    }
}
