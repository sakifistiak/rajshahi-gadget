<footer class="border-t border-border bg-surface" data-tsd-source="/src/components/site/Footer.tsx:35:5">
    <div class="container-page py-14" data-tsd-source="/src/components/site/Footer.tsx:36:7">
        <!-- 1. Store Locations (OUR OUTLETS) -->
        <section aria-label="Our outlets" class="border-b border-border pb-10">
            <div>
                <!-- Centered Header with Full-Width Lines and Dots -->
                <div class="relative flex items-center justify-center my-8 py-2 w-full">
                    <div class="flex-1 flex items-center justify-end">
                        <div class="h-[1px] w-full bg-foreground/40"></div>
                        <div class="h-2 w-2 rounded-full bg-foreground shrink-0 -ml-1"></div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground px-4 sm:px-6 whitespace-nowrap">Our Outlets</h3>
                    <div class="flex-1 flex items-center justify-start">
                        <div class="h-2 w-2 rounded-full bg-foreground shrink-0 -mr-1"></div>
                        <div class="h-[1px] w-full bg-foreground/40"></div>
                    </div>
                </div>

                <ul class="mt-5 grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse($storeLocations as $outlet)
                        <li class="flex gap-2 border-t border-border/60 pt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin mt-0.5 h-3.5 w-3.5 shrink-0 text-muted-foreground" aria-hidden="true">
                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm font-medium leading-tight">{{ $outlet->name }}</p>
                                <div class="mt-1 text-xs leading-relaxed text-muted-foreground whitespace-pre-line">{!! nl2br($outlet->address) !!}</div>
                                @if($outlet->phone)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $outlet->phone) }}" class="mt-1 inline-block text-xs text-muted-foreground hover:text-foreground">{{ $outlet->phone }}</a>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="col-span-full text-center text-xs text-muted-foreground py-4">
                            No store locations added yet.
                        </li>
                    @endforelse
                </ul>
            </div>
        </section>

        <!-- 2. Main Footer Links & Information -->
        @php
            // Build the grid from the columns that are actually visible. This prevents
            // hidden link columns from leaving an empty slot at larger screen sizes.
            // Visibility is controlled by the admin toggle. An enabled column with
            // no links remains intentionally blank, rather than falling back to links.
            $showFooterCol1 = ($footerCol1Active ?? '1') == '1';
            $showFooterCol2 = ($footerCol2Active ?? '1') == '1';
            $footerColumnCount = 2 + (int) $showFooterCol1 + (int) $showFooterCol2;
            $footerGridClass = match ($footerColumnCount) {
                2 => 'grid-cols-1 md:grid-cols-2 max-w-3xl mx-auto',
                3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-w-5xl mx-auto',
                default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4 max-w-6xl mx-auto',
            };
        @endphp
        <div class="mt-12 grid gap-10 md:gap-16 {{ $footerGridClass }}">
            <!-- Brand Column -->
            <div>
                <a aria-label="Khan Gadget home" href="/" class="inline-flex items-center">
                    <img src="{{ $siteLogo ?? '/media/b3ca13-kg-lockup-v2.png' }}" alt="{{ $siteName ?? 'Khan Gadget - Eternal Tech Companion' }}" class="h-14 w-auto object-contain dark:hidden" />
                    <img src="{{ $siteLogoDark ?? ($siteLogo ?? '/media/b3ca13-kg-lockup-v2.png') }}" alt="{{ $siteName ?? 'Khan Gadget - Eternal Tech Companion' }}" class="h-14 w-auto object-contain hidden dark:block" />
                </a>
                <p class="mt-4 text-sm text-muted-foreground">
                    {{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}
                </p>
                <address class="mt-5 space-y-2 text-sm not-italic text-muted-foreground">
                    <div class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                            <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>{{ $siteAddress ?? 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh' }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                            <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>
                        </svg>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone ?? '+8801700000000') }}" class="hover:text-foreground">
                            {{ $sitePhone ?? '+8801700000000' }}
                        </a>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                            <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                        </svg>
                        <a href="mailto:{{ $siteEmail ?? 'khangadget.bd@gmail.com' }}" class="hover:text-foreground">
                            {{ $siteEmail ?? 'khangadget.bd@gmail.com' }}
                        </a>
                    </div>
                </address>

                <div class="mt-5 flex flex-wrap items-center gap-2">
                    <a href="{{ $socialFacebook ?? 'https://facebook.com/khansgadget' }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook" style="background-color:#1877F2" class="grid h-10 w-10 place-items-center rounded-full text-white shadow-sm transition-transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="h-4 w-auto" aria-hidden="true"><path fill="currentColor" d="M80 299.3l0 212.7 116 0 0-212.7 86.5 0 18-97.8-104.5 0 0-34.6c0-51.7 20.3-71.5 72.7-71.5 16.3 0 29.4 .4 37 1.2l0-88.7C291.4 4 256.4 0 236.2 0 129.3 0 80 50.5 80 159.4l0 42.1-66 0 0 97.8 66 0z"/></svg>
                    </a>
                    <a href="{{ $socialYoutube ?? 'https://youtube.com/@khansgadget' }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" title="YouTube" style="background-color:#FF0000" class="grid h-10 w-10 place-items-center rounded-full text-white shadow-sm transition-transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="h-4 w-auto" aria-hidden="true"><path fill="currentColor" d="M549.7 124.1C543.5 100.4 524.9 81.8 501.4 75.5 458.9 64 288.1 64 288.1 64S117.3 64 74.7 75.5C51.2 81.8 32.7 100.4 26.4 124.1 15 167 15 256.4 15 256.4s0 89.4 11.4 132.3c6.3 23.6 24.8 41.5 48.3 47.8 42.6 11.5 213.4 11.5 213.4 11.5s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zM232.2 337.6l0-162.4 142.7 81.2-142.7 81.2z"/></svg>
                    </a>
                    <a href="{{ $socialInstagram ?? 'https://bikroy.com/en/shops/khangadgets' }}" target="_blank" rel="noopener noreferrer" aria-label="Bikroy" title="Bikroy" class="grid h-10 w-10 place-items-center rounded-full border border-border bg-white p-2 shadow-sm transition-transform hover:scale-105">
                        <img src="/media/icon-bikroy.ico" alt="Bikroy" class="h-full w-full object-contain">
                    </a>
                    <a href="{{ $socialWhatsapp ?? 'https://bdstall.com/stall/2373' }}" target="_blank" rel="noopener noreferrer" aria-label="BD Stall" title="BD Stall" class="grid h-10 w-10 place-items-center rounded-full border border-border bg-white p-2 shadow-sm transition-transform hover:scale-105">
                        <img src="/media/icon-bdstall.png" alt="BD Stall" class="h-full w-full object-contain rounded-full">
                    </a>
                    <a href="{{ $socialDaraz ?? 'https://daraz.com.bd/shop/ki2kz4ne' }}" target="_blank" rel="noopener noreferrer" aria-label="Daraz" title="Daraz" class="grid h-10 w-10 place-items-center rounded-full border border-border bg-white p-2 shadow-sm transition-transform hover:scale-105">
                        <img src="/media/icon-daraz.ico" alt="Daraz" class="h-full w-full object-contain">
                    </a>
                    <a href="mailto:{{ $siteEmail ?? 'khangadget.bd@gmail.com' }}" aria-label="Email" title="Email" class="grid h-10 w-10 place-items-center rounded-full border border-border text-muted-foreground transition-colors hover:bg-foreground hover:text-background">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-4 w-4" aria-hidden="true">
                            <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Shop Links Column -->
            @if($showFooterCol1)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em]">{{ $footerCol1Title ?? 'SHOP' }}</p>
                    <ul class="mt-4 space-y-3 text-sm">
                        @foreach($footerCol1Links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="text-muted-foreground hover:text-foreground">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Explore Links Column -->
            @if($showFooterCol2)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em]">{{ $footerCol2Title ?? 'EXPLORE' }}</p>
                    <ul class="mt-4 space-y-3 text-sm">
                        @foreach($footerCol2Links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="text-muted-foreground hover:text-foreground">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Connect Column -->
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.14em]">CONNECT</p>
                <p class="mt-4 text-sm text-muted-foreground">
                    Website: <a href="https://khangadget.com" class="underline-offset-4 hover:underline">khangadget.com</a>
                </p>
            </div>
        </div>

        <!-- 3. Centered Dynamic Copyright Bar -->
        <div class="mt-12 border-t border-border pt-6 text-center text-xs text-muted-foreground">
            <p>© {{ date('Y') }} {{ $footerCopyright ?? 'Khan Gadget. All rights reserved.' }}</p>
        </div>
    </div>
    <div class="h-14 sm:hidden"></div>
</footer>
@include('partials.cart-script')
