<x-app-layout>
    <div class="w-full rounded-md border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4"><div><h2 class="text-base font-bold text-slate-900">Add Category</h2><p class="mt-1 text-xs text-slate-500">Create a category for product organization.</p></div><a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600">Back to list</a></div>
        @if ($errors->any())<div class="mb-5 rounded border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">{{ $errors->first() }}</div>@endif
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">@csrf @include('admin.categories._form')<div class="flex justify-end gap-3"><a href="{{ route('admin.categories.index') }}" class="rounded border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600">Cancel</a><button class="rounded bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">Create Category</button></div></form>
    </div>
</x-app-layout>
