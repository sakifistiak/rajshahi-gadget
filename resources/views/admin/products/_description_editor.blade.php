<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>

<div>
    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Description <span class="text-rose-500">*</span></label>
    <div id="description-quill" style="min-height: 220px; background:#fff;"></div>
    <textarea id="description" name="description" style="display:none">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<script>
(function () {
    if (typeof Quill === 'undefined') return;

    var imageResizeAvailable = false;
    try {
        if (typeof ImageResize !== 'undefined' && Quill.register) {
            Quill.register('modules/imageResize', ImageResize.default || ImageResize);
            imageResizeAvailable = true;
        }
    } catch (e) {}

    var hiddenField = document.getElementById('description');

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

    var modules = {
        toolbar: {
            container: [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link', 'image'],
                ['clean']
            ],
            handlers: { image: imageHandler }
        }
    };
    if (imageResizeAvailable) {
        modules.imageResize = { modules: ['Resize', 'DisplaySize'] };
    }

    var quill = new Quill('#description-quill', {
        theme: 'snow',
        placeholder: 'Write the full product description…',
        modules: modules
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
