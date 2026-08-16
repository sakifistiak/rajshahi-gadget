<x-app-layout>
    <div class="w-full space-y-6" x-data="{ imagePath: '' }">
        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Add Blog Post</h1>
            </div>
            <a href="{{ route('admin.blog-posts.index') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-sm transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Blog Posts
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.blog-posts.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-sm border border-slate-200 shadow-sm space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Title <span class="text-rose-500">*</span></label>
                <input id="title" type="text" name="title" value="{{ old('title') }}" required autofocus
                       class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Slug <span class="font-normal normal-case text-slate-400">(optional — auto-generated from title if left blank)</span></label>
                <input id="slug" type="text" name="slug" value="{{ old('slug') }}"
                       class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Excerpt <span class="text-rose-500">*</span> <span class="font-normal normal-case text-slate-400">(short summary shown on the blog listing card)</span></label>
                <textarea id="excerpt" name="excerpt" rows="2" required maxlength="500"
                          class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">{{ old('excerpt') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category Tag <span class="text-rose-500">*</span></label>
                    <input id="category_tag" type="text" name="category_tag" value="{{ old('category_tag') }}" required placeholder="e.g. Buying Guide"
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Read Minutes</label>
                    <input id="read_minutes" type="number" name="read_minutes" min="1" max="60" value="{{ old('read_minutes', 5) }}"
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Publish Date <span class="font-normal normal-case text-slate-400">(blank = draft)</span></label>
                    <input id="published_at" type="date" name="published_at" value="{{ old('published_at') }}"
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Author Name <span class="text-rose-500">*</span></label>
                    <input id="author_name" type="text" name="author_name" value="{{ old('author_name') }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Author Role</label>
                    <input id="author_role" type="text" name="author_role" value="{{ old('author_role') }}" placeholder="e.g. Reviewer"
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <label class="flex items-center gap-2.5 p-3 bg-slate-50 rounded-sm border border-slate-200 cursor-pointer w-fit">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-xs font-semibold text-slate-700">Featured <span class="font-normal text-slate-400">— show as the hero banner at the top of /blog</span></span>
            </label>

            <!-- Featured Image -->
            <div class="p-4 bg-slate-50 rounded-sm border border-slate-200">
                <label class="block font-bold text-xs text-slate-800 uppercase tracking-wider mb-2">Featured Image</label>
                <p class="text-[11px] text-slate-500 mb-3">Shown on the blog card and at the top of the post. Upload a file, or paste an existing path/URL.</p>
                <input type="hidden" name="featured_image_path" x-model="imagePath">
                <div class="flex items-start gap-3">
                    <div class="h-20 w-20 shrink-0 rounded-sm border border-slate-200 bg-white overflow-hidden flex items-center justify-center" x-show="imagePath">
                        <img :src="imagePath" alt="Featured image preview" class="h-full w-full object-cover">
                    </div>
                    <div class="h-20 w-20 shrink-0 rounded-sm border border-dashed border-slate-300 bg-white flex items-center justify-center" x-show="!imagePath">
                        <i data-lucide="image" class="w-6 h-6 text-slate-300"></i>
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="file" name="featured_image_file" accept="image/*" @change="imagePath = URL.createObjectURL($event.target.files[0])"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded-sm p-1 bg-white">
                        <input type="text" placeholder="or paste an image path/URL" @input="imagePath = $event.target.value" name="featured_image_path_display"
                               class="w-full text-xs px-3 py-2 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Content <span class="text-rose-500">*</span></label>
                <div id="content-quill" style="min-height: 320px; background:#fff;"></div>
                <textarea id="content" name="content" style="display:none">{{ old('content') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.blog-posts.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-sm transition-all">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-sm shadow-sm transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    {{ __('Create Post') }}
                </button>
            </div>
        </form>
    </div>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
    (function () {
        if (typeof Quill === 'undefined') return;
        var hiddenField = document.getElementById('content');

        function imageHandler() {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();
            input.onchange = function () {
                var file = input.files[0];
                if (!file) return;
                var formData = new FormData();
                formData.append('file', file);
                var token = document.querySelector('meta[name="csrf-token"]');
                fetch('{{ route('admin.media.upload') }}', {
                    method: 'POST',
                    headers: token ? { 'X-CSRF-TOKEN': token.content } : {},
                    body: formData
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            var range = quill.getSelection(true) || { index: quill.getLength() };
                            quill.insertEmbed(range.index, 'image', data.url, 'user');
                            quill.setSelection(range.index + 1);
                        } else {
                            alert('Image upload failed.');
                        }
                    })
                    .catch(function () { alert('Image upload failed.'); });
            };
        }

        var quill = new Quill('#content-quill', {
            theme: 'snow',
            placeholder: 'Write the blog post…',
            modules: {
                toolbar: {
                    container: [
                        [{ header: [2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'link', 'image'],
                        ['clean']
                    ],
                    handlers: { image: imageHandler }
                }
            }
        });

        if (hiddenField.value) {
            quill.root.innerHTML = hiddenField.value;
        }

        quill.on('text-change', function () {
            hiddenField.value = quill.root.innerHTML;
        });

        var form = hiddenField.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                hiddenField.value = quill.root.innerHTML;
            });
        }
    })();
    </script>
</x-app-layout>
