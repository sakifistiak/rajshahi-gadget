<x-app-layout>
    <div class="w-full space-y-6" x-data="{ imagePath: '' }">
        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-slate-200 shadow-sm">
            <h1 class="text-xl font-bold text-slate-900">Add Customer Spotlight</h1>
            <a href="{{ route('admin.customer-spotlights.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-sm transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Spotlights
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2"><i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.customer-spotlights.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-sm border border-slate-200 shadow-sm space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Customer Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Location <span class="text-rose-500">*</span></label>
                    <input type="text" name="location" value="{{ old('location') }}" required placeholder="e.g. Dhaka" class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Product <span class="text-rose-500">*</span></label>
                    <input type="text" name="product" value="{{ old('product') }}" required placeholder="e.g. MacBook Pro 14 M1 Pro" class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date') }}" required class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Quote <span class="text-rose-500">*</span></label>
                <textarea name="quote" rows="3" required maxlength="1000" placeholder="What did the customer say?" class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">{{ old('quote') }}</textarea>
            </div>

            <div class="p-4 bg-slate-50 rounded-sm border border-slate-200">
                <label class="block font-bold text-xs text-slate-800 uppercase tracking-wider mb-2">Photo</label>
                <input type="hidden" name="image_path" x-model="imagePath">
                <div class="flex items-start gap-3">
                    <div class="h-20 w-20 shrink-0 rounded-sm border border-slate-200 bg-white overflow-hidden flex items-center justify-center" x-show="imagePath">
                        <img :src="imagePath" class="h-full w-full object-cover">
                    </div>
                    <div class="h-20 w-20 shrink-0 rounded-sm border border-dashed border-slate-300 bg-white flex items-center justify-center" x-show="!imagePath">
                        <i data-lucide="image" class="w-6 h-6 text-slate-300"></i>
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="file" name="image_file" accept="image/*" @change="imagePath = URL.createObjectURL($event.target.files[0])" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded-sm p-1 bg-white">
                        <input type="text" placeholder="or paste an image path/URL" @input="imagePath = $event.target.value" class="w-full text-xs px-3 py-2 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.customer-spotlights.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-sm transition-all">{{ __('Cancel') }}</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-sm shadow-sm transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>{{ __('Add Spotlight') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
