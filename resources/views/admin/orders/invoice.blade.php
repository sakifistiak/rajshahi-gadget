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

    <!-- html2pdf for Direct Client-side PDF Downloads -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Inter', 'Hind Siliguri', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-wrapper {
            border: none !important;
            box-shadow: none !important;
        }

        @page {
            size: A4 portrait;
            margin: 6mm 10mm;
        }

        @media print {
            html, body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                height: 100% !important;
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
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .page-break-avoid {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }

        /* Dynamic scaling presets */
        .invoice-scale-compact {
            font-size: 11px !important;
        }
        .invoice-scale-compact .py-3.5 {
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
        }
        .invoice-scale-compact .py-2.5 {
            padding-top: 0.3rem !important;
            padding-bottom: 0.3rem !important;
        }
        .invoice-scale-compact .space-y-2 > :not([hidden]) ~ :not([hidden]) {
            margin-top: 0.35rem !important;
        }

        .invoice-scale-very-compact {
            font-size: 10px !important;
        }
        .invoice-scale-very-compact .py-3.5,
        .invoice-scale-very-compact .py-3 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
        }
        .invoice-scale-very-compact .py-2.5,
        .invoice-scale-very-compact .py-2 {
            padding-top: 0.2rem !important;
            padding-bottom: 0.2rem !important;
        }
        .invoice-scale-very-compact .space-y-2 > :not([hidden]) ~ :not([hidden]) {
            margin-top: 0.2rem !important;
        }
        .invoice-scale-very-compact .mt-6 {
            margin-top: 0.75rem !important;
        }
    </style>
</head>
<body class="min-h-screen py-6 px-4 sm:px-6 lg:px-8">
@php
    $itemCount = $order->items->count();
    $scaleClass = match(true) {
        $itemCount >= 6 => 'invoice-scale-very-compact',
        $itemCount >= 3 => 'invoice-scale-compact',
        default => '',
    };
@endphp

    <!-- Top Action Bar (Screen Only) -->
    @php
        $isPublic = $public ?? false;
    @endphp
    <div class="no-print max-w-4xl mx-auto mb-4 flex flex-wrap items-center justify-between gap-4 bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            @if($isPublic)
                <a href="{{ route('thank-you') }}?order={{ urlencode($order->order_number) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Back to Order
                </a>
            @else
                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Back to Order
                </a>
            @endif
            <span class="text-xs text-slate-300">|</span>
            <span class="text-xs text-slate-600 font-medium">Order <strong class="text-slate-900 font-bold">#{{ $order->order_number }}</strong></span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="downloadInvoicePDF()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded shadow-sm transition-colors cursor-pointer">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                Download PDF
            </button>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow-sm transition-colors cursor-pointer">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                Print Invoice
            </button>
            @unless($isPublic)
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="list" class="w-3.5 h-3.5"></i>
                    All Orders
                </a>
            @endunless
        </div>
    </div>

    <!-- Normal Flat Invoice Document (No outer border box) -->
    <div class="invoice-wrapper {{ $scaleClass }} max-w-4xl mx-auto bg-white p-6 sm:p-8">
        
        <!-- Top Header: Logo, Company info & Invoice Details -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-4 border-b border-slate-200">
            <!-- Left: Company Branding & Details -->
            <div class="space-y-1 max-w-md">
                @if(!empty($company['logo']))
                    <div class="mb-1.5">
                        <img src="{{ asset($company['logo']) }}" alt="{{ $company['name'] }}" class="h-9 sm:h-10 w-auto object-contain" />
                    </div>
                @else
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $company['name'] }}</h1>
                @endif

                <div class="text-xs text-slate-600 space-y-0.5 leading-snug">
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
            <div class="text-left sm:text-right space-y-0.5 shrink-0">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-wider">INVOICE</h2>
                <div class="text-sm font-bold text-blue-600">
                    <span class="text-slate-400 font-normal">Order ID:</span>
                    {{ $order->order_number }}
                </div>

                <div class="pt-1 text-xs space-y-0.5 text-slate-600">
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

        <!-- Customer & Delivery Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-3.5 border-b border-slate-200 text-xs">
            <!-- Customer Info -->
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Customer Details</span>
                <h3 class="text-sm font-bold text-slate-900">{{ $order->customer_name }}</h3>
                <div class="text-xs text-slate-700 space-y-0.5">
                    <p>Phone: <span class="font-semibold">{{ $order->phone }}</span></p>
                    @if($order->email)
                        <p>Email: {{ $order->email }}</p>
                    @endif
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="space-y-1">
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Delivery Details</span>
                    <span class="text-[10px] font-bold text-slate-500">({{ $order->delivery_method === 'store_pickup' ? 'Store Pickup' : 'Courier Delivery' }})</span>
                </div>

                @if($order->delivery_method === 'store_pickup')
                    <div class="text-xs text-slate-700 space-y-0.5">
                        @if($order->storeLocation)
                            <p class="font-bold text-slate-900">{{ $order->storeLocation->name }}</p>
                            <div class="text-slate-600 leading-tight">{!! $order->storeLocation->address !!}</div>
                            @if($order->storeLocation->phone)
                                <p class="text-slate-600">Contact: {{ preg_replace('/^Contact:\s*/i', '', $order->storeLocation->phone) }}</p>
                            @endif
                        @else
                            <p class="text-slate-500 italic">Pickup Outlet</p>
                        @endif
                    </div>
                @else
                    <div class="text-xs text-slate-700 space-y-1">
                        <div>
                            <p class="font-semibold text-slate-900 leading-snug">{{ $order->address }}</p>
                        </div>

                        @if($order->delivery_area)
                            <p class="text-slate-600 leading-tight">
                                <span class="font-medium text-slate-700">{{ $order->delivery_area === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }}</span>
                            </p>
                        @endif

                        @if($order->note)
                            <p class="text-slate-500 text-[10.5px] italic pt-0.5"><span class="font-semibold text-slate-600 not-italic">Note:</span> {{ $order->note }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Ordered Items Table -->
        <div class="py-3.5">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-300 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-2 px-2 w-8 text-center">#</th>
                        <th class="py-2 px-2">Item Description</th>
                        <th class="py-2 px-2 text-right w-24">Unit Price</th>
                        <th class="py-2 px-2 text-center w-16">Qty</th>
                        <th class="py-2 px-2 text-right w-28">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach ($order->items as $index => $item)
                        <tr>
                            <td class="py-2.5 px-2 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-2">
                                <div class="font-bold text-slate-900 leading-snug">{{ $item->product_name }}</div>
                            </td>
                            <td class="py-2.5 px-2 text-right font-medium text-slate-700">
                                ৳{{ number_format($item->unit_price) }}
                            </td>
                            <td class="py-2.5 px-2 text-center font-semibold text-slate-800">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-2.5 px-2 text-right font-bold text-slate-900">
                                ৳{{ number_format($item->line_total) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary, Terms & Totals -->
        <div class="page-break-avoid border-t border-slate-300 pt-3 flex flex-col sm:flex-row justify-between items-start gap-4">
            <!-- Left: Terms and Conditions -->
            <div class="w-full sm:w-7/12 space-y-1.5 text-xs text-slate-600">
                <div class="space-y-2">
                    <!-- Conditions: Intact -->
                    <div>
                        <span class="font-bold text-slate-800 uppercase tracking-wide text-[11px] block mb-0.5">Conditions: (For Intact Laptop Only)</span>
                        <p class="text-[10px] text-slate-600 leading-snug">
                            Warranty Coverage Shown On The Website Is Based On Our Purchase Date From The Brand. Your Warranty Will Be Counted From The Date Of Your Purchase From Us, And You Will Receive 1 Year Warranty. The Duration Or Years Displayed On The Official Site Does Not Apply. Carry Cost To Be Paid By Customer For International Warranty Claim (+ 4 To 8 No. Conditions Apply).
                        </p>
                    </div>

                    <!-- Conditions: PRE-OWNED -->
                    <div>
                        <span class="font-bold text-slate-800 uppercase tracking-wide text-[11px] block mb-0.5">Conditions: (FOR PRE-OWNED &amp; OPEN BOX LAPTOP ONLY)</span>
                        <div class="text-[9.5px] text-slate-600 space-y-0.5 leading-snug">
                            <div class="flex items-start gap-1">
                                <span class="shrink-0 font-medium">1.</span>
                                <span>10 Days Parts Replacement Guarantee Without Display, Adapter &amp; Casing. If The Same Model/Variant Is Unavailable, Then With Any Available Product Of The Same Or Higher Price Range By Adjusting The Price Accordingly, As Decided By {{ $company['name'] }}.</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <span class="shrink-0 font-medium">2.</span>
                                <span>5 Years Service Warranty Without Parts. If Service Is Not Possible, Then New Parts Need To Be Purchased By Customer.</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <span class="shrink-0 font-medium">3.</span>
                                <span>70% Cash Is Refundable In Case Of Return After Buying Within 7 Days, Exchange Value To Be Determined By {{ $company['name'] }}.</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <span class="shrink-0 font-medium">4.</span>
                                <span>The Warranty/Guarantee Is Not Applicable For Any: Physical Damage, Internal Burn, Warranty Sticker Damage/Removal Etc.</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <span class="shrink-0 font-medium">5.</span>
                                <span>If Any Product Is Lost Or Damaged Through The Courier Service, The Customer Will Contact The Courier Company, {{ $company['name'] }} Authority Is Not Responsible.</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <span class="shrink-0 font-medium">6.</span>
                                <span>After Sales Service Is Only Available At Service Center, Service Center Off Days: Dhaka: Tuesday, Rajshahi: Friday &amp; Sunday, Bogura: Friday &amp; Saturday.</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <span class="shrink-0 font-medium">7.</span>
                                <span>If Product Has No Fault As Per Deal/Advertisement &amp; Customer Changes His/Her Mind Without Any Logical/Valid Reason, Pre-Order/Pre-Booked Money Won't Be Refunded.</span>
                            </div>
                            <div class="flex items-start gap-1">
                                <span class="shrink-0 font-medium">8.</span>
                                <span>Online/Courier-Based Orders Imply Acceptance Of All Terms, Even Without Customer Signature.</span>
                            </div>
                        </div>
                    </div>

                    <!-- NB Section -->
                    <div class="text-[9px] text-slate-500 pt-1 border-t border-slate-200 space-y-0.5 leading-snug bg-slate-50 p-2 rounded">
                        <p><strong class="text-slate-700">NB:</strong></p>
                        <p>• <strong class="text-slate-700">Replacement Means:</strong> ১০ দিনের মধ্যে বিনামূল্যে যন্ত্রাংশ পরিবর্তন করে দেওয়া।</p>
                        <p>• <strong class="text-slate-700">Exchange Means:</strong> একটি পণ্যের পরিবর্তে অন্য পণ্য নেয়া; এক্ষেত্রে কাস্টোমারের পণ্যের মূল্য খান গ্যাজেট ক্রয় বিভাগ নির্ধারণ করবে।</p>
                        <p>• <strong class="text-slate-700">Refund Means:</strong> গ্রাহক ক্রয়ের ৭ দিনের মধ্যে বিনা ক্ষতিতে পণ্য ফেরত দিলে ক্রয়মূল্যের ৭০% টাকা পাবে। ৭ দিন পার হবার পর এই সুযোগ আর নেই।</p>
                    </div>
                </div>
            </div>

            <!-- Right: Calculation Breakdown (Clean Normal Flat) -->
            <div class="w-full sm:w-4/12 space-y-1 text-xs shrink-0">
                <div class="flex justify-between text-slate-600 py-0.5">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-900">৳{{ number_format($order->subtotal) }}</span>
                </div>
                <div class="flex justify-between text-slate-600 py-0.5">
                    <span>Shipping Fee:</span>
                    <span class="font-semibold text-slate-900">
                        @if($order->shipping_fee > 0)
                            ৳{{ number_format($order->shipping_fee) }}
                        @else
                            Free
                        @endif
                    </span>
                </div>
                <div class="border-t border-slate-300 pt-2 flex justify-between items-baseline">
                    <span class="text-xs font-bold text-slate-900">Grand Total:</span>
                    <span class="text-lg font-extrabold text-blue-600">৳{{ number_format($order->total) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Signatures -->
        <div class="page-break-avoid mt-6 pt-3 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-end gap-6">
            <div class="text-left">
                <div class="w-36 border-b border-slate-400 mb-0.5"></div>
                <p class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider">Customer Signature</p>
            </div>

            <div class="text-center sm:text-right">
                <div class="w-36 border-b border-slate-400 mb-0.5 ml-auto"></div>
                <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">{{ $company['name'] }}</p>
                <p class="text-[10px] text-slate-400">Authorized Signature & Seal</p>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="page-break-avoid mt-3 pt-2 border-t border-slate-100 text-center text-[10px] text-slate-400">
            Thank you for shopping with <strong>{{ $company['name'] }}</strong>!
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function downloadInvoicePDF() {
            const wrapper = document.querySelector('.invoice-wrapper');
            if (!wrapper) return;

            const originalWidth = wrapper.style.width;
            const originalMaxWidth = wrapper.style.maxWidth;
            const originalPadding = wrapper.style.padding;
            
            wrapper.style.width = '794px';
            wrapper.style.maxWidth = '794px';
            wrapper.style.padding = '20px 28px';

            const opt = {
                margin: [4, 6, 4, 6],
                filename: 'Invoice-{{ $order->order_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true, 
                    allowTaint: true, 
                    logging: false,
                    windowWidth: 800
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
            };

            return html2pdf().set(opt).from(wrapper).save().then(function() {
                wrapper.style.width = originalWidth;
                wrapper.style.maxWidth = originalMaxWidth;
                wrapper.style.padding = originalPadding;
            }).catch(function(err) {
                console.error("PDF download failed:", err);
                wrapper.style.width = originalWidth;
                wrapper.style.maxWidth = originalMaxWidth;
                wrapper.style.padding = originalPadding;
            });
        }

        function ensureHtml2PdfAndRun(callback) {
            if (typeof html2pdf !== 'undefined') {
                setTimeout(callback, 250);
            } else {
                let attempts = 0;
                const interval = setInterval(() => {
                    attempts++;
                    if (typeof html2pdf !== 'undefined') {
                        clearInterval(interval);
                        setTimeout(callback, 250);
                    } else if (attempts > 60) {
                        clearInterval(interval);
                        console.error("html2pdf failed to load in time.");
                    }
                }, 50);
            }
        }

        function runWhenReady() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('download') === '1') {
                ensureHtml2PdfAndRun(downloadInvoicePDF);
            } else if (urlParams.get('print') === '1') {
                setTimeout(function() { window.print(); }, 300);
            }
        }

        if (document.readyState === 'complete') {
            runWhenReady();
        } else {
            window.addEventListener('load', runWhenReady);
        }

        function autoFitInvoiceToA4() {
            const wrapper = document.querySelector('.invoice-wrapper');
            if (!wrapper) return;
            // Printable A4 target height in px (approx ~1050px)
            const targetMaxHeight = 1040;
            const currentHeight = wrapper.scrollHeight;
            if (currentHeight > targetMaxHeight) {
                const scale = Math.max(0.72, Math.floor((targetMaxHeight / currentHeight) * 100) / 100);
                wrapper.style.transform = `scale(${scale})`;
                wrapper.style.transformOrigin = 'top center';
            } else {
                wrapper.style.transform = '';
            }
        }

        window.addEventListener('beforeprint', autoFitInvoiceToA4);
        window.addEventListener('afterprint', function() {
            const wrapper = document.querySelector('.invoice-wrapper');
            if (wrapper) wrapper.style.transform = '';
        });
    </script>
</body>
</html>
