<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'customer_id' => 'required|exists:customers,id',
            'currency_id' => 'required|exists:currencies,id',
            'items' => 'required|array',
            'items.*.product_name' => 'required|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.total' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = $this->orderService->createOrder($request->all());

        return response()->json(['message' => 'Order created successfully', 'order' => $order->load('items')], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);

        $order = $this->orderService->updateOrderStatus($order, $request->status, $request->note);

        return response()->json(['message' => 'Order status updated', 'order' => $order]);
    }
}
