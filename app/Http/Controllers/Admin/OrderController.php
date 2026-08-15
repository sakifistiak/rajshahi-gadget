<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return $this->listOrders($request);
    }

    private function listOrders(Request $request)
    {
        $query = Order::with(['items', 'storeLocation']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $groupedCounts = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusCounts = [
            'all' => array_sum($groupedCounts),
            'pending' => $groupedCounts['pending'] ?? 0,
            'processing' => $groupedCounts['processing'] ?? 0,
            'shipped' => $groupedCounts['shipped'] ?? 0,
            'delivered' => $groupedCounts['delivered'] ?? 0,
            'cancelled' => $groupedCounts['cancelled'] ?? 0,
        ];

        $routeName = 'admin.orders.index';
        $pageTitle = 'Order Management';
        $pageSubtitle = 'Track and manage customer orders, update status and view details.';

        return view('admin.orders.index', compact('orders', 'statusCounts', 'routeName', 'pageTitle', 'pageSubtitle'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'storeLocation']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
