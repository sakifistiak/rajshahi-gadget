<x-app-layout>
    <div class="w-full space-y-6">
        <div class="flex items-center justify-between p-5 bg-white rounded-sm border border-slate-200 shadow-sm">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Payment Gateway</h2>
                <p class="text-xs text-slate-500 mt-1">SSLCommerz online payment (card, bKash, Nagad, net banking) at checkout.</p>
            </div>
            @if ($active)
                <span class="px-2.5 py-1 text-[11px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Live at checkout{{ $settings['sslcommerz_sandbox'] === '1' ? ' (Sandbox)' : '' }}</span>
            @else
                <span class="px-2.5 py-1 text-[11px] font-bold rounded bg-slate-100 text-slate-600 border border-slate-200">Hidden at checkout</span>
            @endif
        </div>

        @if (session('success'))
            <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-sm text-xs font-bold space-y-1">
                <p>{{ session('success') }}</p>
                @if (session('test_url'))
                    <p class="font-semibold">Test payment page: <a href="{{ session('test_url') }}" target="_blank" rel="noopener" class="underline break-all">{{ session('test_url') }}</a></p>
                @endif
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 border border-red-200 rounded-sm text-xs font-bold">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 border border-red-200 rounded-sm text-xs font-bold">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('admin.payment-gateway.update') }}" method="POST" class="space-y-5" autocomplete="off">
            @csrf

            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-5">
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">SSLCommerz</h3>
                        <p class="text-xs text-slate-500 mt-1">Show "Online Payment" at checkout. It only appears once a Store ID and Store Password are saved.</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="sslcommerz_enabled" value="1" class="peer sr-only" {{ $settings['sslcommerz_enabled'] === '1' ? 'checked' : '' }}>
                        <span class="h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-blue-600 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                    </label>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Store ID</label>
                        <input type="text" name="sslcommerz_store_id" value="{{ old('sslcommerz_store_id', $settings['sslcommerz_store_id']) }}" placeholder="yourstore0live" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Store Password</label>
                        <input type="password" name="sslcommerz_store_password" value="" autocomplete="new-password" placeholder="{{ $settings['has_password'] ? 'Saved - leave blank to keep it' : 'Store password from SSLCommerz' }}" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @if ($settings['has_password'])
                            <label class="flex items-center gap-2 text-[11px] text-slate-500"><input type="checkbox" name="remove_store_password" value="1"> Remove the saved password</label>
                        @endif
                        <p class="text-[11px] text-slate-400">Stored encrypted. It is never shown again after saving.</p>
                    </div>
                </div>

                <label class="flex items-start gap-3 rounded-md border border-slate-200 p-4 cursor-pointer">
                    <input type="checkbox" name="sslcommerz_sandbox" value="1" class="mt-0.5" {{ $settings['sslcommerz_sandbox'] === '1' ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-bold text-slate-800">Sandbox (test) mode</span>
                        <span class="block text-xs text-slate-500 mt-1">Keep this on with sandbox credentials. Turn it off only after SSLCommerz approves the live store and you have entered the live Store ID and Password. Sandbox payments are not real money.</span>
                    </span>
                </label>

                <div class="border-t border-slate-100 pt-5 space-y-2">
                    <h3 class="text-sm font-bold text-slate-800">URLs for the SSLCommerz merchant panel</h3>
                    <p class="text-xs text-slate-500">Set this as the IPN URL in your SSLCommerz merchant panel. The other three are sent automatically with each payment.</p>
                    <div class="grid gap-2 text-xs">
                        <div class="flex flex-wrap gap-2"><span class="w-20 font-bold text-slate-700">IPN</span><code class="font-mono text-slate-800 break-all select-all">{{ route('payment.sslcommerz.ipn') }}</code></div>
                        <div class="flex flex-wrap gap-2"><span class="w-20 font-bold text-slate-500">Success</span><code class="font-mono text-slate-500 break-all">{{ route('payment.sslcommerz.success') }}</code></div>
                        <div class="flex flex-wrap gap-2"><span class="w-20 font-bold text-slate-500">Fail</span><code class="font-mono text-slate-500 break-all">{{ route('payment.sslcommerz.fail') }}</code></div>
                        <div class="flex flex-wrap gap-2"><span class="w-20 font-bold text-slate-500">Cancel</span><code class="font-mono text-slate-500 break-all">{{ route('payment.sslcommerz.cancel') }}</code></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end"><button class="rounded-sm bg-blue-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-700">Save Payment Gateway Settings</button></div>
        </form>

        <form action="{{ route('admin.payment-gateway.test') }}" method="POST" class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
            @csrf
            <div>
                <h3 class="text-sm font-bold text-slate-800">Test Connection</h3>
                <p class="text-xs text-slate-500 mt-1">Uses the saved credentials to open a 10 BDT test payment session with SSLCommerz. No order is created and nothing is charged.</p>
            </div>
            <button class="rounded-sm border border-blue-600 px-5 py-2.5 text-xs font-bold text-blue-600 hover:bg-blue-50">Test Connection</button>
        </form>
    </div>
</x-app-layout>
