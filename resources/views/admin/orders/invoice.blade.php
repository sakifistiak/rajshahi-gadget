<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} - {{ $company['name'] }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset($siteFavicon ?? '/favicon.png') }}" type="image/png"/>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', 'Hind Siliguri', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }

        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .invoice-wrapper {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }
            .page-break-avoid {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-4 bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Back to Order
            </a>
            <span class="text-xs text-slate-300">|</span>
            <span class="text-xs text-slate-600 font-medium">Order <strong class="text-slate-900 font-bold">#{{ $order->order_number }}</strong></span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow-sm transition-colors">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                Print Invoice
            </button>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                <i data-lucide="list" class="w-3.5 h-3.5"></i>
                All Orders
            </a>
        </div>
    </div>

    <!-- Normal Flat Invoice Document -->
    <div class="invoice-wrapper max-w-4xl mx-auto bg-white border border-slate-200 shadow-sm p-8 sm:p-12">
        
        <!-- Top Header: Logo, Company info & Invoice Details -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-8 pb-8 border-b border-slate-200">
            <!-- Left: Company Branding & Details -->
            <div class="space-y-2 max-w-md">
                @if(!empty($company['logo']))
                    <div class="mb-3">
                        <img src="{{ asset($company['logo']) }}" alt="{{ $company['name'] }}" class="h-10 sm:h-12 w-auto object-contain" />
                    </div>
                @else
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $company['name'] }}</h1>
                @endif
                
                @if(!empty($company['slogan']))
                    <p class="text-xs font-medium text-slate-500">{{ $company['slogan'] }}</p>
                @endif

                <div class="text-xs text-slate-600 space-y-1 pt-1 leading-relaxed">
                    @if(!empty($company['address']))
                        <p>{{ $company['address'] }}</p>
                    @endif
                    @if(!empty($company['phone']))
                        <p>Phone: <strong>{{ $company['phone'] }}</strong></p>
                    @endif
                    @if(!empty($company['email']))
                        <p>Email: {{ $company['email'] }}</p>
                    @endif
                </div>
            </div>

            <!-- Right: Invoice Metadata -->
            <div class="text-left sm:text-right space-y-1.5 shrink-0">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-wider">INVOICE</h2>
                <div class="text-xs text-slate-500">Invoice / Order #</div>
                <div class="text-sm font-bold text-blue-600">{{ $order->order_number }}</div>

                <div class="pt-2 text-xs space-y-1 text-slate-600">
                    <div>
                        <span class="text-slate-400">Date:</span>
                        <span class="font-medium text-slate-800 ml-1">{{ $order->created_at->format('d M, Y - h:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Payment:</span>
                        <span class="font-bold text-slate-800 ml-1 uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Status:</span>
                        <span class="font-bold text-slate-800 ml-1 uppercase">{{ $order->status }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Normal Customer & Delivery Section (No Rounded Boxes) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-6 border-b border-slate-200">
            <!-- Customer Info -->
            <div class="space-y-1.5">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Customer Details</span>
                <h3 class="text-sm font-bold text-slate-900">{{ $order->customer_name }}</h3>
                <div class="text-xs text-slate-700 space-y-1">
                    <p>Phone: <span class="font-semibold">{{ $order->phone }}</span></p>
                    @if($order->email)
                        <p>Email: {{ $order->email }}</p>
                    @endif
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Delivery Details</span>
                    <span class="text-[10px] font-bold text-slate-600">({{ $order->delivery_method === 'store_pickup' ? 'Store Pickup' : 'Home Delivery' }})</span>
                </div>

                @if($order->delivery_method === 'store_pickup')
                    <div class="text-xs text-slate-700 space-y-0.5">
                        @if($order->storeLocation)
                            <p class="font-bold text-slate-900">{{ $order->storeLocation->name }}</p>
                            <div class="text-slate-600 leading-relaxed">{!! $order->storeLocation->address !!}</div>
                            @if($order->storeLocation->phone)
                                <p class="text-slate-600 mt-1">Contact: {{ preg_replace('/^Contact:\s*/i', '', $order->storeLocation->phone) }}</p>
                            @endif
                        @else
                            <p class="text-slate-500 italic">Pickup Outlet</p>
                        @endif
                    </div>
                @else
                    <div class="text-xs text-slate-700 space-y-0.5">
                        <p class="font-medium text-slate-900 leading-relaxed">{{ $order->address }}</p>
                        <p class="text-slate-600">
                            @if($order->union_area){{ $order->union_area }}, @endif
                            @if($order->upazila){{ $order->upazila }}, @endif
                            {{ $order->district }}
                            @if($order->division && $order->division !== $order->district) - {{ $order->division }}@endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Ordered Items Table -->
        <div class="py-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-300 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-2.5 px-2 w-10 text-center">#</th>
                        <th class="py-2.5 px-2">Item Description</th>
                        <th class="py-2.5 px-2 text-right w-28">Unit Price</th>
                        <th class="py-2.5 px-2 text-center w-20">Qty</th>
                        <th class="py-2.5 px-2 text-right w-32">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach ($order->items as $index => $item)
                        <tr>
                            <td class="py-3 px-2 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="py-3 px-2">
                                <div class="font-bold text-slate-900 leading-snug">{{ $item->product_name }}</div>
                            </td>
                            <td class="py-3 px-2 text-right font-medium text-slate-700">
                                ৳{{ number_format($item->unit_price) }}
                            </td>
                            <td class="py-3 px-2 text-center font-semibold text-slate-800">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-3 px-2 text-right font-bold text-slate-900">
                                ৳{{ number_format($item->line_total) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Normal Summary & Totals (No Box) -->
        <div class="page-break-avoid border-t border-slate-300 pt-4 flex flex-col sm:flex-row justify-between items-start gap-8">
            <!-- Left: Payment Details & Instructions -->
            <div class="w-full sm:w-1/2 space-y-2 text-xs text-slate-600">
                <div>
                    <span class="font-bold text-slate-800">Payment Details:</span>
                    <p class="mt-0.5">Method: <strong class="text-slate-900 uppercase">{{ $order->payment_method }}</strong></p>
                    @if($order->payment_method === 'cod')
                        <p class="text-slate-500">Amount payable to delivery personnel upon package arrival.</p>
                    @endif
                </div>

                <div class="text-[11px] text-slate-500 space-y-0.5 pt-2">
                    <p>• Please check the package and product condition in front of the delivery agent.</p>
                    <p>• Warranty claims require preserving this invoice copy.</p>
                </div>
            </div>

            <!-- Right: Calculation Breakdown (Clean Normal Flat) -->
            <div class="w-full sm:w-5/12 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600 py-1">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-900">৳{{ number_format($order->subtotal) }}</span>
                </div>
                <div class="flex justify-between text-slate-600 py-1">
                    <span>Shipping / Delivery Fee:</span>
                    <span class="font-semibold text-slate-900">
                        @if($order->shipping_fee > 0)
                            ৳{{ number_format($order->shipping_fee) }}
                        @else
                            Free
                        @endif
                    </span>
                </div>
                <div class="border-t border-slate-300 pt-2 flex justify-between items-baseline">
                    <span class="text-sm font-bold text-slate-900">Grand Total:</span>
                    <span class="text-lg font-extrabold text-blue-600">৳{{ number_format($order->total) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Signatures -->
        <div class="page-break-avoid mt-16 pt-6 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-end gap-10">
            <div class="text-left">
                <div class="w-40 border-b border-slate-400 mb-1"></div>
                <p class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider">Customer Signature</p>
            </div>

            <div class="text-center sm:text-right">
                <div class="w-44 border-b border-slate-400 mb-1 ml-auto"></div>
                <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">{{ $company['name'] }}</p>
                <p class="text-[10px] text-slate-400">Authorized Signature & Seal</p>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="page-break-avoid mt-8 pt-4 border-t border-slate-100 text-center text-[11px] text-slate-400">
            Thank you for shopping with <strong>{{ $company['name'] }}</strong>!
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
