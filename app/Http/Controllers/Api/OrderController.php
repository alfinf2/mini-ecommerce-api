<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $orders = Order::with(['user', 'products'])->paginate(10);
        } else {
            $orders = Order::with('products')->where('user_id', $user->id)->paginate(10);
        }

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        $user = auth()->user();
        $order = DB::transaction(function () use ($validated, $user) {
            $total = 0;
            $attach = [];
            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $total += $product->price * $qty;
                $attach[$product->id] = ['quantity' => $qty];
            }
            $order = Order::create(['user_id' => $user->id, 'total_price' => $total]);
            $order->products()->attach($attach);

            return $order;
        });
        $order->load('products', 'user');

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat',
            'data' => $order,
        ], 201);
    }

    public function show(Order $order)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $order->load('user', 'products');

        return response()->json($order);
    }

    public function update(Request $request, Order $order)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'total_price' => 'sometimes|required|numeric',
            'user_id' => 'sometimes|required|exists:users,id',
        ]);
        $order->update($validated);
        $order->load('user', 'products');

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil diupdate',
            'data' => $order,
        ]);
    }

    public function destroy(Order $order)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dihapus',
        ], 200);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $order,
        ]);
    }

    public function dashboard()
    {
        return response()->json([
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'paid')->sum('total_price'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
        ]);
    }

    public function cancel(Order $order)
    {
        // Pastikan order milik user yang login
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        // Hanya bisa cancel jika masih pending
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Order cannot be cancelled',
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'message' => 'Order cancelled successfully',
            'data' => $order,
        ]);
    }
}
