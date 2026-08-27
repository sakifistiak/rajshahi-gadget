<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout | {{ $siteName ?? 'Khan Gadget' }}</title>
    <link rel="stylesheet" href="/assets/styles-CC_Lznyw.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="/assets/theme.js"></script>
    <style>
        /* The static styles-CC_Lznyw.css bundle is missing fractional-value and
           sm:/lg: responsive utility classes entirely, so the checkout form's
           layout, spacing and input sizing has to be hand-covered here instead
           of relying on Tailwind classes like gap-1.5, py-2.5, sm:grid-cols-2,
           lg:grid-cols-[...] which silently do nothing in this bundle. */
        html, body { overflow-x: hidden; }
        /* Grid/flex items default to min-width:auto, which sizes them to fit
           their content (e.g. a long un-wrapped product name) even past the
           viewport. min-width:0 lets them shrink instead of pushing the page
           into horizontal overflow on narrow screens. */
        #checkout-form, #checkout-form * { min-width: 0; }
        #checkout-form { display: grid; gap: 24px; grid-template-columns: 1fr; }
        #checkout-form .co-field { display: grid; gap: 6px; }
        #checkout-form .co-field input,
        #checkout-form .co-field textarea,
        #checkout-form .co-field select { padding: 10px 12px; width: 100%; box-sizing: border-box; }
        .co-delivery-method { display: flex; flex-direction: column; gap: 12px; }
        .co-delivery-grid { display: grid; gap: 16px; grid-template-columns: 1fr; }
        .co-span-2 { grid-column: 1 / -1; }
        @media (min-width: 640px) {
            .co-delivery-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (min-width: 1024px) {
            #checkout-form { grid-template-columns: minmax(0, 1fr) 380px; }
            #checkout-form aside { position: sticky; top: 24px; align-self: start; }
        }
        /* The outlet dropdown must show each store name exactly as entered in the
           admin — plain, never bold or italic — regardless of what the wrapping
           <label class="font-medium"> or the static CSS bundle would inherit. */
        #pickup-field select,
        #pickup-field select option {
            font-weight: 400 !important;
            font-style: normal !important;
            font-family: inherit;
        }
    </style>
</head>
<body class="min-h-screen bg-background text-foreground">
    @include('partials.header')

    <main class="container-page py-8 sm:py-12">
        <div class="mb-8 text-center"><p class="text-sm text-muted-foreground">Secure checkout</p><h1 class="mt-1 text-3xl font-semibold">Complete your order</h1></div>
        <div id="checkout-empty" class="hidden rounded-lg border border-border p-8 text-center">
            <p>Your cart is empty.</p><a href="/shop" class="mt-4 inline-block font-semibold underline">Continue shopping</a>
        </div>
        <form id="checkout-form" novalidate>
            <section class="space-y-6">
                <div class="rounded-lg border border-border bg-card p-5">
                    <h2 class="text-lg font-semibold">Delivery information</h2>
                    <div class="mt-5 co-delivery-grid">
                        <label class="co-field co-span-2 text-sm font-medium">Full name<input required name="customer_name" class="rounded-md border border-border bg-background font-normal" autocomplete="name"></label>
                        <label class="co-field text-sm font-medium">Mobile number<input required name="phone" inputmode="tel" class="rounded-md border border-border bg-background font-normal" placeholder="01XXXXXXXXX" autocomplete="tel"></label>
                        <label class="co-field text-sm font-medium">Email <span class="font-normal text-muted-foreground">(optional)</span><input name="email" type="email" class="rounded-md border border-border bg-background font-normal" autocomplete="email"></label>
                        <label class="co-field co-span-2 text-sm font-medium" id="pickup-field" style="display:none">Pickup outlet
                            <select name="store_location_id" class="rounded-md border border-border bg-background font-normal">
                                <option value="">Select an outlet</option>
                                @foreach($storeLocations as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="co-field text-sm font-medium" id="division-field">Division
                            <select name="division" class="rounded-md border border-border bg-background font-normal" id="division-select" disabled>
                                <option value="">Loading…</option>
                            </select>
                        </label>
                        <label class="co-field text-sm font-medium" id="district-field">District
                            <select name="district" class="rounded-md border border-border bg-background font-normal" id="district-select" disabled>
                                <option value="">Select district</option>
                            </select>
                        </label>
                        <label class="co-field text-sm font-medium" id="upazila-field">Upazila / Thana
                            <select name="upazila" class="rounded-md border border-border bg-background font-normal" id="upazila-select" disabled>
                                <option value="">Select upazila</option>
                            </select>
                        </label>
                        <label class="co-field text-sm font-medium" id="postal-field"><span>Postal Code <span class="font-normal text-muted-foreground">(optional)</span></span><input name="postal_code" inputmode="numeric" class="rounded-md border border-border bg-background font-normal" placeholder="e.g. 1205" autocomplete="postal-code"></label>
                        <label class="co-field co-span-2 text-sm font-medium" id="address-field">Specific Address<textarea required name="address" rows="3" class="rounded-md border border-border bg-background font-normal" placeholder="House, road, village/area details"></textarea></label>
                        <label class="co-field co-span-2 text-sm font-medium">Order note <span class="font-normal text-muted-foreground">(optional)</span><textarea name="note" rows="2" class="rounded-md border border-border bg-background font-normal"></textarea></label>
                    </div>
                </div>
                <div class="rounded-lg border border-border bg-card p-5">
                    <h2 class="text-lg font-semibold">Delivery method</h2>
                    <div class="mt-4 co-delivery-method">
                        <label class="flex cursor-pointer items-center gap-3 rounded-md border border-border p-4">
                            <input type="radio" name="delivery_method" value="home_delivery" id="dm-home" checked>
                            <span><strong>Courier Delivery</strong><br><span class="text-sm text-muted-foreground">Delivery at nearest location — pay when it arrives.</span></span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-md border border-border p-4">
                            <input type="radio" name="delivery_method" value="store_pickup" id="dm-pickup">
                            <span><strong>Store / Outlet Pickup</strong><br><span class="text-sm text-muted-foreground">Collect your order from one of our outlets, free of charge.</span></span>
                        </label>
                    </div>
                </div>
                <div class="rounded-lg border border-border bg-card p-4 sm:p-5" id="payment-method-card">
                    <h2 class="text-base sm:text-lg font-semibold">Payment method</h2>
                    <div class="mt-3.5 space-y-2.5 sm:space-y-3">
                        <!-- 1. Cash on Delivery (Active) -->
                        <label class="flex cursor-pointer items-center justify-between gap-2.5 sm:gap-3 rounded-md border-2 border-primary bg-background p-3 sm:p-4 transition-colors">
                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                <input checked type="radio" name="payment_method" value="cod" class="h-4 w-4 text-primary focus:ring-primary shrink-0">
                                <div class="w-14 sm:w-16 h-8 sm:h-9 px-1 py-1 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-md flex items-center justify-center shrink-0 overflow-hidden">
                                    <img src="/assets/payment/cod.svg" alt="COD" class="h-full w-full object-contain" />
                                </div>
                                <div class="min-w-0">
                                    <strong class="text-xs sm:text-sm font-bold text-foreground block leading-tight">Cash on Delivery</strong>
                                    <p class="text-[11px] sm:text-xs text-muted-foreground mt-0.5 leading-tight">Pay when your order arrives</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-[11px] font-bold rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 shrink-0 whitespace-nowrap">
                                Available
                            </span>
                        </label>

                        <!-- 2. bKash (Coming Soon) -->
                        <div class="flex items-center justify-between gap-2.5 sm:gap-3 rounded-md border border-border/70 bg-secondary/30 p-3 sm:p-4 opacity-90 cursor-not-allowed">
                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                <input type="radio" disabled class="h-4 w-4 opacity-40 cursor-not-allowed shrink-0">
                                <div class="w-14 sm:w-16 h-8 sm:h-9 px-1 py-1 bg-white rounded-md border border-slate-200 shadow-xs flex items-center justify-center shrink-0 overflow-hidden">
                                    <img src="/assets/payment/bkash.png" alt="bKash" class="h-full w-full object-contain" />
                                </div>
                                <div class="min-w-0">
                                    <strong class="text-xs sm:text-sm font-semibold text-foreground/90 block leading-tight">bKash Payment</strong>
                                    <p class="text-[11px] sm:text-xs text-muted-foreground mt-0.5 leading-tight">Direct app & gateway</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-[11px] font-bold rounded bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 shrink-0 whitespace-nowrap">
                                Coming Soon
                            </span>
                        </div>

                        <!-- 3. Nagad (Coming Soon) -->
                        <div class="flex items-center justify-between gap-2.5 sm:gap-3 rounded-md border border-border/70 bg-secondary/30 p-3 sm:p-4 opacity-90 cursor-not-allowed">
                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                <input type="radio" disabled class="h-4 w-4 opacity-40 cursor-not-allowed shrink-0">
                                <div class="w-14 sm:w-16 h-8 sm:h-9 px-1 py-1 bg-white rounded-md border border-slate-200 shadow-xs flex items-center justify-center shrink-0 overflow-hidden">
                                    <img src="/assets/payment/nagad.png" alt="Nagad" class="h-full w-full object-contain" />
                                </div>
                                <div class="min-w-0">
                                    <strong class="text-xs sm:text-sm font-semibold text-foreground/90 block leading-tight">Nagad Payment</strong>
                                    <p class="text-[11px] sm:text-xs text-muted-foreground mt-0.5 leading-tight">Digital wallet payment</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-[11px] font-bold rounded bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 shrink-0 whitespace-nowrap">
                                Coming Soon
                            </span>
                        </div>

                        <!-- 4. Card / Bank & EMI (Coming Soon) -->
                        <div class="flex items-center justify-between gap-2.5 sm:gap-3 rounded-md border border-border/70 bg-secondary/30 p-3 sm:p-4 opacity-90 cursor-not-allowed">
                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                <input type="radio" disabled class="h-4 w-4 opacity-40 cursor-not-allowed shrink-0">
                                <div class="w-14 sm:w-16 h-8 sm:h-9 px-1 py-1 bg-white rounded-md border border-slate-200 shadow-xs flex items-center justify-center shrink-0 overflow-hidden">
                                    <img src="/assets/payment/cards.svg" alt="Cards & Bank" class="h-full w-full object-contain" />
                                </div>
                                <div class="min-w-0">
                                    <strong class="text-xs sm:text-sm font-semibold text-foreground/90 block leading-tight">Card / Bank & EMI</strong>
                                    <p class="text-[11px] sm:text-xs text-muted-foreground mt-0.5 leading-tight">Visa, Master & Net Banking</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-[11px] font-bold rounded bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 shrink-0 whitespace-nowrap">
                                Coming Soon
                            </span>
                        </div>
                    </div>
                </div>
            </section>
            <aside class="rounded-lg border border-border bg-card p-5"><h2 class="text-lg font-semibold">Order summary</h2><div id="checkout-items" class="mt-5 space-y-4"></div><div class="mt-5 space-y-2 border-t border-border pt-4 text-sm"><div class="flex justify-between"><span>Subtotal</span><strong id="checkout-subtotal"></strong></div><div class="flex justify-between"><span>Delivery</span><strong id="checkout-delivery-fee">Free</strong></div><div class="flex justify-between border-t border-border pt-3 text-base"><strong>Total</strong><strong id="checkout-total"></strong></div></div><p id="checkout-error" class="mt-4 hidden text-sm text-red-600 dark:text-red-400"></p><button id="place-order" type="submit" class="mt-6 w-full rounded-full bg-primary font-bold text-primary-foreground transition" style="padding:12px 16px">Place order</button></aside>
        </form>
    </main>
    @include('partials.footer', ['hideOutlets' => true])
    @include('partials.mobile-drawer')
    <script>
    (function () {
        var cartKey = 'kg_shopping_cart';
        var buyNow = @json($buyNow);
        var items = buyNow ? [buyNow] : JSON.parse(localStorage.getItem(cartKey) || '[]');
        var money = function (n) { return '৳ ' + Number(n || 0).toLocaleString('en-IN'); };
        var escapeHtml = function (s) { var d=document.createElement('div'); d.textContent=s || ''; return d.innerHTML; };
        var total = items.reduce(function (sum, item) { return sum + Number(item.price || 0) * Number(item.quantity || 1); }, 0);
        if (!items.length) { document.getElementById('checkout-form').classList.add('hidden'); document.getElementById('checkout-empty').classList.remove('hidden'); return; }
        document.getElementById('checkout-items').innerHTML = items.map(function (item) { return '<div class="flex gap-3"><img class="h-14 w-14 rounded object-cover bg-secondary" src="' + escapeHtml(item.image) + '" alt=""><div class="min-w-0 flex-1"><p class="text-sm font-medium">' + escapeHtml(item.name) + '</p><p class="mt-1 text-sm text-muted-foreground">Qty: ' + Number(item.quantity || 1) + '</p></div><strong class="text-sm">' + money(Number(item.price || 0) * Number(item.quantity || 1)) + '</strong></div>'; }).join('');
        document.getElementById('checkout-subtotal').textContent = money(total);

        var shippingFeeInsideDhaka = {{ (int) $shippingFeeInsideDhaka }};
        var shippingFeeOutsideDhaka = {{ (int) $shippingFeeOutsideDhaka }};

        (function () {
            // Two delivery methods: Courier Delivery (Cash on Delivery — needs
            // the address fields + a fee based on district) and Store /
            // Outlet Pickup (needs the outlet dropdown, always free). Whichever
            // group isn't relevant is hidden AND disabled — a disabled control
            // is excluded from both native form validation and FormData, so it
            // can't block submission or get sent to the server by mistake.
            var homeOnlyFields = ['division-field', 'district-field', 'upazila-field', 'postal-field', 'address-field'].map(function (id) { return document.getElementById(id); });
            var pickupField = document.getElementById('pickup-field');
            var paymentCard = document.getElementById('payment-method-card');
            var storeSelect = document.querySelector('[name="store_location_id"]');
            var addressInput = document.querySelector('#address-field textarea');
            var postalInput = document.querySelector('#postal-field input');
            var divisionSelect = document.getElementById('division-select');
            var districtSelect = document.getElementById('district-select');
            var upazilaSelect = document.getElementById('upazila-select');
            var deliveryFeeEl = document.getElementById('checkout-delivery-fee');
            var totalEl = document.getElementById('checkout-total');
            var geoData = null;

            function isPickupMode() {
                return document.querySelector('[name="delivery_method"]:checked').value === 'store_pickup';
            }

            function updateDeliveryFee() {
                if (isPickupMode()) {
                    deliveryFeeEl.textContent = 'Free';
                    totalEl.textContent = money(total);
                    return;
                }
                if (!districtSelect.value) {
                    deliveryFeeEl.textContent = 'Select district';
                    totalEl.textContent = money(total);
                    return;
                }
                var fee = districtSelect.value === 'Dhaka' ? shippingFeeInsideDhaka : shippingFeeOutsideDhaka;
                deliveryFeeEl.textContent = fee > 0 ? money(fee) : 'Free';
                totalEl.textContent = money(total + fee);
            }

            function refreshFieldStates() {
                var pickup = isPickupMode();

                homeOnlyFields.forEach(function (el) { el.style.display = pickup ? 'none' : ''; });
                pickupField.style.display = pickup ? '' : 'none';
                if (paymentCard) {
                    paymentCard.style.display = pickup ? 'none' : '';
                }

                storeSelect.disabled = !pickup;
                storeSelect.required = pickup;

                addressInput.disabled = pickup;
                addressInput.required = !pickup;
                postalInput.disabled = pickup;

                divisionSelect.disabled = pickup || !geoData;
                divisionSelect.required = !pickup;

                districtSelect.disabled = pickup || !geoData || !divisionSelect.value;
                districtSelect.required = !pickup;

                upazilaSelect.disabled = pickup || !geoData || !districtSelect.value;
                upazilaSelect.required = !pickup;

                updateDeliveryFee();
            }

            document.querySelectorAll('[name="delivery_method"]').forEach(function (radio) {
                radio.addEventListener('change', refreshFieldStates);
            });

            function fillOptions(select, list, placeholder) {
                select.innerHTML = '<option value="">' + placeholder + '</option>' + list.map(function (item) {
                    return '<option value="' + item.name.replace(/"/g, '&quot;') + '">' + item.name + '</option>';
                }).join('');
            }

            fetch('/data/bd-geo.json').then(function (res) { return res.json(); }).then(function (geo) {
                geoData = geo;
                fillOptions(divisionSelect, geo.divisions, 'Select division');

                divisionSelect.addEventListener('change', function () {
                    var division = geo.divisions.find(function (d) { return d.name === divisionSelect.value; });
                    districtSelect.innerHTML = '<option value="">Select district</option>';
                    upazilaSelect.innerHTML = '<option value="">Select upazila</option>';
                    if (division) {
                        var districts = geo.districts.filter(function (d) { return d.division_id === division.id; });
                        fillOptions(districtSelect, districts, 'Select district');
                    }
                    refreshFieldStates();
                });

                districtSelect.addEventListener('change', function () {
                    var district = geo.districts.find(function (d) { return d.name === districtSelect.value; });
                    upazilaSelect.innerHTML = '<option value="">Select upazila</option>';
                    if (district) {
                        var upazilas = geo.upazilas.filter(function (u) { return u.district_id === district.id; });
                        fillOptions(upazilaSelect, upazilas, 'Select upazila');
                    }
                    refreshFieldStates();
                });

                refreshFieldStates();
            }).catch(function () {
                divisionSelect.innerHTML = '<option value="">Could not load — refresh the page</option>';
                refreshFieldStates();
            });

            refreshFieldStates();
        })();

        document.getElementById('checkout-form').addEventListener('submit', async function (event) {
            event.preventDefault(); var form = event.currentTarget, button = document.getElementById('place-order'), error = document.getElementById('checkout-error');
            if (!form.reportValidity()) return;
            error.classList.add('hidden'); button.disabled = true; button.textContent = 'Placing order…';
            var data = Object.fromEntries(new FormData(form).entries()); data.items = items.map(function (item) { return { slug: item.slug, quantity: Number(item.quantity || 1) }; });
            try { var response = await fetch('/orders', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(data) }); var payload = await response.json(); if (!response.ok) throw new Error(payload.message || Object.values(payload.errors || {})[0]?.[0] || 'Could not place the order.'); if (!buyNow) localStorage.removeItem(cartKey); window.location.href = '/thank-you?order=' + encodeURIComponent(payload.order_number); } catch (err) { error.textContent = err.message; error.classList.remove('hidden'); button.disabled = false; button.textContent = 'Place order'; }
        });
    })();
    </script>
</body></html>
