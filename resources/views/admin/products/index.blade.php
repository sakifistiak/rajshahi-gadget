<x-app-layout>

    @if (session('success'))
        <div class="mb-5 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
            <i data-lucide="alert-circle" class="h-4 w-4 text-rose-600"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Product Catalog Panel -->
    <div x-data="{
             selected: [],
             rowIds() { return Array.from(document.querySelectorAll('#products-results tbody input[type=checkbox]')).map(c => Number(c.value)); },
             get allSelected() { const ids = this.rowIds(); return ids.length > 0 && ids.every(id => this.selected.includes(id)); },
             toggleAll(checked) { this.selected = checked ? this.rowIds() : []; },
         }"
         x-on:products-updated.window="selected = []"
         class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">

        <!-- Header Controls -->
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800">{{ __('Catalog List') }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Manage details of all products uploaded to the catalog.</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.products.bulk-destroy') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the selected products?');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="return" value="{{ request()->fullUrl() }}">
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" x-show="selected.length > 0" x-cloak :disabled="selected.length === 0" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors">
                        <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                        <span x-text="`{{ __('Delete Selected') }} (${selected.length})`"></span>
                    </button>
                </form>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors">
                    <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                    {{ __('Add New Product') }}
                </a>
            </div>
        </div>

        <!-- Search & Filters -->
        <form id="product-filter-form" action="{{ route('admin.products.index') }}" method="GET" class="px-5 py-3 border-b border-slate-100 bg-white flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="h-3.5 w-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="q" value="{{ request('q') }}" autocomplete="off" placeholder="Search by product name..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-slate-200 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                <span id="product-search-spinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></span>
            </div>
            <select name="category" class="px-3 py-1.5 text-xs border border-slate-200 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none bg-white cursor-pointer">
                <option value="">{{ __('All Categories') }}</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string) request('category') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="condition" class="px-3 py-1.5 text-xs border border-slate-200 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none bg-white cursor-pointer">
                <option value="">{{ __('All Conditions') }}</option>
                @foreach ($conditions as $cond)
                    <option value="{{ $cond->id }}" {{ (string) request('condition') === (string) $cond->id ? 'selected' : '' }}>{{ $cond->label }}</option>
                @endforeach
            </select>
            <select name="brand" class="px-3 py-1.5 text-xs border border-slate-200 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none bg-white cursor-pointer">
                <option value="">{{ __('All Brands') }}</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ (string) request('brand') === (string) $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
            <select name="stock" class="px-3 py-1.5 text-xs border border-slate-200 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none bg-white cursor-pointer">
                <option value="">{{ __('All Stock Status') }}</option>
                <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
            </select>
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors cursor-pointer">
                <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                {{ __('Filter') }}
            </button>
            @if (request('q') || request('category') || request('condition') || request('brand') || request('stock'))
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] font-bold uppercase rounded transition-colors">
                    <i data-lucide="x" class="h-3.5 w-3.5"></i>
                    {{ __('Reset') }}
                </a>
            @endif
        </form>

        <div id="products-results" class="transition-opacity duration-150">
            @include('admin.products._results')
        </div>
    </div>

    <script>
        (function () {
            const form    = document.getElementById('product-filter-form');
            const results = document.getElementById('products-results');
            if (!form || !results) return;
            const search  = form.querySelector('input[name="q"]');
            const spinner = document.getElementById('product-search-spinner');

            let debounce;
            let reqToken = 0;

            function listUrl(overrides = {}) {
                const params = new URLSearchParams(new FormData(form));
                for (const [k, v] of [...params]) {
                    if (v === '' || v === null) params.delete(k);
                }
                Object.entries(overrides).forEach(([k, v]) => {
                    (v === '' || v === null || v === undefined) ? params.delete(k) : params.set(k, v);
                });
                const qs = params.toString();
                return form.action + (qs ? '?' + qs : '');
            }

            async function load(url, { spin = false } = {}) {
                const token = ++reqToken;
                results.classList.add('opacity-40', 'pointer-events-none');
                if (spin && spinner) spinner.classList.remove('hidden');
                try {
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error('Request failed');
                    const html = await res.text();
                    if (token !== reqToken) return; // a newer request already fired
                    results.innerHTML = html;
                    history.replaceState(null, '', url);
                    window.dispatchEvent(new CustomEvent('products-updated'));
                    if (window.lucide) lucide.createIcons();
                    if (window.Alpine && window.Alpine.initTree) window.Alpine.initTree(results);
                } catch (e) {
                    window.location.assign(url); // graceful fallback to a full reload
                } finally {
                    if (token === reqToken) {
                        results.classList.remove('opacity-40', 'pointer-events-none');
                        if (spinner) spinner.classList.add('hidden');
                    }
                }
            }

            // Live search as you type
            search.addEventListener('input', function () {
                clearTimeout(debounce);
                debounce = setTimeout(() => load(listUrl(), { spin: true }), 300);
            });

            // Enter / Filter button
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                clearTimeout(debounce);
                load(listUrl(), { spin: true });
            });

            // Dropdown filters
            form.querySelectorAll('select').forEach(function (sel) {
                sel.addEventListener('change', () => load(listUrl(), { spin: true }));
            });

            // Pagination links inside the swapped region
            results.addEventListener('click', function (e) {
                const link = e.target.closest('a');
                if (link && results.contains(link) && link.href) {
                    e.preventDefault();
                    load(link.href);
                    results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        })();
    </script>
</x-app-layout>
