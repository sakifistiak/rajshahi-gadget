<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SiteSetting;
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

    public function invoice(Order $order)
    {
        $order->load(['items.product.specs', 'storeLocation']);

        $company = [
            'name' => SiteSetting::getValue('site_name', 'Khan Gadget'),
            'slogan' => SiteSetting::getValue('site_slogan', 'Brand NEW Intact BOX, Without BOX & Pre-Owned'),
            'logo' => SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png'),
            'phone' => SiteSetting::getValue('site_phone', '+8801700000000'),
            'whatsapp' => SiteSetting::getValue('whatsapp_number', '8801700000001'),
            'email' => SiteSetting::getValue('site_email', 'khangadget.bd@gmail.com'),
            'address' => SiteSetting::getValue('site_address', 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh'),
            'business_hours' => SiteSetting::getValue('site_business_hours', 'Sat – Thu · 10:00 AM – 9:00 PM'),
        ];

        return view('admin.orders.invoice', compact('order', 'company'));
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
