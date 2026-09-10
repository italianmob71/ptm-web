<link rel="stylesheet" href="{{ asset('css/ckeditor5.css') }}">
<script src="{{ asset('js/ptm-editor.js') }}"></script>
<script>
window.csrfToken = @json(csrf_token());
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-research-editor]').forEach(textarea => {
        (window.PTMEditor.default || window.PTMEditor).create(textarea, {
            licenseKey: 'GPL',
            ckfinder: {
                uploadUrl: @json(route('admin.images.ckeditor')),
                requestHeaders: { 'X-CSRF-TOKEN': window.csrfToken }
            }
        }).then(editor => {
            window.ckeditorInstance = editor;
            textarea.form.addEventListener('submit', () => { textarea.value = editor.getData(); });
        }).catch(error => console.error('CKEditor init error:', error));
    });
});
</script>
<style>
.research-admin input, .research-admin textarea { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-border); border-radius: 0.5rem; background: var(--color-surface); color: var(--color-text); }
.research-admin label { display: block; font-weight: 500; margin-bottom: 0.25rem; }
.research-admin .ck-editor__editable { min-height: 240px; background: var(--color-surface) !important; color: var(--color-text) !important; }
.research-admin .ck-toolbar { background: var(--color-surface-2) !important; color: var(--color-text) !important; }
.research-admin .ck-toolbar button { color: var(--color-text) !important; }
</style>
