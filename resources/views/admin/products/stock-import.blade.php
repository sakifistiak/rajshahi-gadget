<x-app-layout>
    <div class="max-w-6xl mx-auto space-y-5" id="stock-import-page">
        @if (session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-slate-800">{{ __('Import Stock & Price CSV') }}</h1>
                <p class="text-xs text-slate-500 mt-1">{{ __('Upload a CSV, review its contents, then confirm the update.') }}</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600">← {{ __('Back to Products') }}</a>
        </div>

        <div class="bg-white rounded-md border border-slate-200 shadow-sm p-5">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">{{ __('1. Select CSV file') }}</h2>
                    <p class="text-[11px] text-slate-400 mt-1">{{ __('Required columns: product_id, price, stock_quantity. compare_at_price is optional.') }}</p>
                </div>
                <a href="{{ route('admin.products.export-stock') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i>
                    {{ __('Download Latest CSV') }}
                </a>
            </div>

            <input id="stock-file" type="file" accept=".csv,text/csv" class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded p-1 bg-white">
            <p id="file-error" class="hidden mt-2 text-xs font-semibold text-rose-600"></p>
        </div>

        <div id="preview-card" class="hidden bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">{{ __('2. Preview') }}</h2>
                    <p id="preview-summary" class="text-[11px] text-slate-500 mt-1"></p>
                </div>
                <span class="text-[10px] text-slate-400">{{ __('Showing up to 50 rows') }}</span>
            </div>
            <div class="overflow-x-auto max-h-[520px]">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead id="preview-head" class="bg-slate-50 sticky top-0"></thead>
                    <tbody id="preview-body" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" id="clear-file" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded">{{ __('Choose Another File') }}</button>
                <form id="import-form" action="{{ route('admin.products.import-stock') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="stock_file" id="import-file" class="hidden">
                    <button type="submit" id="confirm-import" disabled class="px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-xs font-bold rounded">
                        {{ __('Confirm & Import') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const fileInput = document.getElementById('stock-file');
            const importFile = document.getElementById('import-file');
            const previewCard = document.getElementById('preview-card');
            const previewHead = document.getElementById('preview-head');
            const previewBody = document.getElementById('preview-body');
            const summary = document.getElementById('preview-summary');
            const error = document.getElementById('file-error');
            const confirmButton = document.getElementById('confirm-import');

            function parseCsv(text) {
                const rows = [];
                let row = [], cell = '', quoted = false;
                text = text.replace(/^\uFEFF/, '');
                for (let i = 0; i < text.length; i++) {
                    const char = text[i], next = text[i + 1];
                    if (char === '"' && quoted && next === '"') { cell += '"'; i++; continue; }
                    if (char === '"') { quoted = !quoted; continue; }
                    if (char === ',' && !quoted) { row.push(cell.trim()); cell = ''; continue; }
                    if ((char === '\n' || char === '\r') && !quoted) {
                        if (char === '\r' && next === '\n') i++;
                        row.push(cell.trim()); cell = '';
                        if (row.some(value => value !== '')) rows.push(row);
                        row = [];
                        continue;
                    }
                    cell += char;
                }
                if (cell || row.length) { row.push(cell.trim()); rows.push(row); }
                return rows;
            }

            function showError(message) {
                error.textContent = message;
                error.classList.remove('hidden');
                previewCard.classList.add('hidden');
                confirmButton.disabled = true;
            }

            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                error.classList.add('hidden');
                if (!file) return;
                if (!file.name.toLowerCase().endsWith('.csv')) return showError('Please select a CSV file.');

                const reader = new FileReader();
                reader.onload = () => {
                    const rows = parseCsv(reader.result);
                    if (rows.length < 2) return showError('The CSV has no data rows.');
                    const headers = rows[0].map(header => header.toLowerCase().replace(/^\uFEFF/, '').trim());
                    const required = ['product_id', 'price', 'stock_quantity'];
                    const missing = required.filter(column => !headers.includes(column));
                    if (missing.length) return showError('Missing required column(s): ' + missing.join(', '));

                    previewHead.innerHTML = '<tr>' + headers.map(header => `<th class="px-4 py-2 text-left text-[10px] font-bold text-slate-500 uppercase whitespace-nowrap">${header}</th>`).join('') + '</tr>';
                    previewBody.innerHTML = rows.slice(1, 51).map(row => '<tr>' + headers.map((_, index) => `<td class="px-4 py-2 text-xs text-slate-700 whitespace-nowrap">${(row[index] || '').replaceAll('<', '&lt;').replaceAll('>', '&gt;')}</td>`).join('') + '</tr>').join('');
                    summary.textContent = `${rows.length - 1} data row(s) found in ${file.name}`;
                    previewCard.classList.remove('hidden');
                    confirmButton.disabled = false;

                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    importFile.files = transfer.files;
                };
                reader.onerror = () => showError('Could not read this file.');
                reader.readAsText(file, 'UTF-8');
            });

            document.getElementById('clear-file').addEventListener('click', () => {
                fileInput.value = '';
                importFile.value = '';
                previewCard.classList.add('hidden');
                confirmButton.disabled = true;
            });

            document.getElementById('import-form').addEventListener('submit', (event) => {
                if (!confirm('This will update stock and prices for all rows in the CSV. Continue?')) event.preventDefault();
            });
        })();
    </script>
</x-app-layout>
