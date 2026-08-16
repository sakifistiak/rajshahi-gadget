<x-app-layout>

    @if (session('success'))
        <div class="mb-5 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800">{{ __('Blog Posts') }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Manage articles shown on the public /blog page. Leave "Publish Date" empty to keep a post as a draft.</p>
            </div>
            <a href="{{ route('admin.blog-posts.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                {{ __('Add New Post') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Image') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Title') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Category') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Author') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($posts as $post)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <img src="{{ $post->featured_image ?: '/assets/laptop-ultrabook-C5nU_6_f.jpg' }}" alt="{{ $post->title }}" class="h-8 w-8 object-cover rounded border border-slate-100 shadow-sm">
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-semibold text-slate-800">{{ $post->title }}</span>
                                    @if ($post->is_featured)
                                        <span class="px-1.5 py-0.5 inline-flex items-center gap-0.5 text-[9px] leading-4 font-bold rounded bg-amber-50 text-amber-600 border border-amber-100"><i data-lucide="star" class="h-2.5 w-2.5"></i>Featured</span>
                                    @endif
                                </div>
                                <div class="text-[9px] text-slate-400">/blog/{{ $post->slug }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600">{{ $post->category_tag }}</td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600">{{ $post->author_name }}</td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if ($post->published_at && $post->published_at->lte(now()))
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-emerald-50 text-emerald-600 border border-emerald-100">Published</span>
                                @elseif ($post->published_at)
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-amber-50 text-amber-600 border border-amber-100">Scheduled: {{ $post->published_at->format('M d, Y') }}</span>
                                @else
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-slate-100 text-slate-500 border border-slate-200/50">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="{{ route('admin.blog-posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                                        <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                                        {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.blog-posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center gap-1">
                                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-xs text-slate-400">{{ __('No blog posts yet') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($posts->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/20">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
