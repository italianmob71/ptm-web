/**
 * ImagePicker — custom CKEditor 5 plugin for PTM
 *
 * Adds a toolbar button with an image icon. When clicked, opens a modal
 * overlay with three tabs: Upload New, Search Library, and URL.
 *
 * Upload → posts to /admin/images/ckeditor-upload, inserts returned URL
 * Search → fetches /admin/images/search?q=..., shows grid with "Use Image" buttons
 * URL → user pastes a URL, inserts <img> at cursor
 *
 * Cancel at any stage closes the modal. No editor changes.
 */

import { Plugin, ButtonView, ImageUtils } from 'ckeditor5';

export default class ImagePicker extends Plugin {
    static get requires() {
        return [ImageUtils];
    }

    static get pluginName() {
        return 'ImagePicker';
    }

    init() {
        const editor = this.editor;
        const t = editor.t;

        // Expose the open-modal function on the editor instance
        editor.ui.componentFactory.add('imagePicker', locale => {
            const view = new ButtonView(locale);

            view.set({
                label: t('Insert Image'),
                icon: pickerIcon,
                tooltip: true
            });

            view.on('execute', () => {
                this.openModal(editor);
            });

            return view;
        });
    }

    openModal(editor) {
        // Remove any existing modal
        const existing = document.getElementById('ptm-image-picker-overlay');
        if (existing) existing.remove();

        // Build overlay
        const overlay = document.createElement('div');
        overlay.id = 'ptm-image-picker-overlay';
        overlay.style.cssText = [
            'position:fixed', 'inset:0', 'z-index:10000',
            'background:rgba(0,0,0,0.6)', 'display:flex',
            'align-items:flex-start', 'justify-content:center',
            'padding-top:5vh', 'overflow-y:auto'
        ].join(';');

        // Build modal box
        const modal = document.createElement('div');
        modal.style.cssText = [
            'background:var(--color-surface,#fff)', 'border:1px solid var(--color-border,#ddd)',
            'border-radius:12px', 'width:90%', 'max-width:720px',
            'max-height:88vh', 'overflow-y:auto',
            'box-shadow:0 8px 32px rgba(0,0,0,0.3)', 'padding:0'
        ].join(';');

        modal.innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid var(--color-border,#ddd);">
                <h3 style="margin:0;font-family:var(--font-serif,serif);font-size:1.25rem;color:var(--color-text,#333);">Insert Image</h3>
                <button id="ptm-ip-close" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--color-text-muted,#999);padding:0 0.5rem;line-height:1;">×</button>
            </div>

            <div style="display:flex;border-bottom:1px solid var(--color-border,#ddd);">
                <button class="ptm-ip-tab" data-tab="upload" style="flex:1;padding:0.75rem;background:none;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;border-bottom:3px solid var(--color-accent,#c5a572);color:var(--color-text,#333);">Upload New</button>
                <button class="ptm-ip-tab" data-tab="search" style="flex:1;padding:0.75rem;background:none;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;border-bottom:3px solid transparent;color:var(--color-text-muted,#999);">Search Library</button>
                <button class="ptm-ip-tab" data-tab="url" style="flex:1;padding:0.75rem;background:none;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;border-bottom:3px solid transparent;color:var(--color-text-muted,#999);">URL</button>
            </div>

            <div style="padding:1.5rem;">
                <!-- Upload Tab -->
                <div class="ptm-ip-panel" data-panel="upload">
                    <div id="ptm-ip-drop" style="border:2px dashed var(--color-border,#ccc);border-radius:8px;padding:2rem;text-align:center;cursor:pointer;transition:border-color 0.2s;">
                        <p style="color:var(--color-text-muted,#999);font-size:0.875rem;margin:0;">Click or drag an image here to upload</p>
                        <p style="color:var(--color-text-faint,#ccc);font-size:0.75rem;margin-top:0.5rem;">JPG, PNG, GIF, WebP, SVG — max 10MB</p>
                        <input type="file" id="ptm-ip-file" accept="image/*" style="display:none;">
                    </div>
                    <div id="ptm-ip-preview" style="margin-top:1rem;display:none;text-align:center;">
                        <img id="ptm-ip-preview-img" style="max-width:100%;max-height:200px;border-radius:8px;">
                        <p id="ptm-ip-file-info" style="font-size:0.75rem;color:var(--color-text-muted,#999);margin-top:0.5rem;"></p>
                    </div>
                    <div style="margin-top:1rem;">
                        <input type="text" id="ptm-ip-alt" placeholder="Alt text (optional)" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                    </div>
                    <div style="display:flex;gap:0.75rem;margin-top:1rem;">
                        <button id="ptm-ip-upload-btn" style="padding:0.5rem 1.5rem;background:var(--color-accent,#c5a572);color:var(--color-text-inv,#fff);border:none;border-radius:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">Upload & Insert</button>
                        <button id="ptm-ip-cancel-upload" style="padding:0.5rem 1.5rem;background:none;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;cursor:pointer;color:var(--color-text-muted,#999);">Cancel</button>
                    </div>
                    <div id="ptm-ip-upload-error" style="margin-top:0.75rem;color:var(--color-danger,#dc2626);font-size:0.75rem;display:none;"></div>
                </div>

                <!-- Search Tab -->
                <div class="ptm-ip-panel" data-panel="search" style="display:none;">
                    <div style="display:flex;gap:0.5rem;margin-bottom:1rem;">
                        <input type="text" id="ptm-ip-search-input" placeholder="Search images by name, alt text, or category..." style="flex:1;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                        <button id="ptm-ip-search-btn" style="padding:0.5rem 1rem;background:none;border:1px solid var(--color-accent,#c5a572);color:var(--color-accent,#c5a572);border-radius:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">Search</button>
                    </div>
                    <div id="ptm-ip-search-results" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:0.75rem;max-height:400px;overflow-y:auto;"></div>
                </div>

                <!-- URL Tab -->
                <div class="ptm-ip-panel" data-panel="url" style="display:none;">
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:0.8125rem;color:var(--color-text,#333);margin-bottom:0.5rem;">Image URL</label>
                        <input type="text" id="ptm-ip-url-input" placeholder="https://example.com/image.jpg" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:0.8125rem;color:var(--color-text,#333);margin-bottom:0.5rem;">Alt text (optional)</label>
                        <input type="text" id="ptm-ip-url-alt" placeholder="Descriptive text for accessibility" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button id="ptm-ip-url-btn" style="padding:0.5rem 1.5rem;background:var(--color-accent,#c5a572);color:var(--color-text-inv,#fff);border:none;border-radius:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">Insert Image</button>
                        <button id="ptm-ip-cancel-url" style="padding:0.5rem 1.5rem;background:none;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;cursor:pointer;color:var(--color-text-muted,#999);">Cancel</button>
                    </div>
                </div>
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Close handlers
        const closeModal = () => overlay.remove();

        modal.querySelector('#ptm-ip-close').addEventListener('click', closeModal);
        modal.querySelector('#ptm-ip-cancel-upload').addEventListener('click', closeModal);
        modal.querySelector('#ptm-ip-cancel-url').addEventListener('click', closeModal);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });

        // Tab switching
        modal.querySelectorAll('.ptm-ip-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                // Update tab styles
                modal.querySelectorAll('.ptm-ip-tab').forEach(t => {
                    t.style.borderBottomColor = 'transparent';
                    t.style.color = 'var(--color-text-muted,#999)';
                });
                tab.style.borderBottomColor = 'var(--color-accent,#c5a572)';
                tab.style.color = 'var(--color-text,#333)';
                // Show/hide panels
                modal.querySelectorAll('.ptm-ip-panel').forEach(p => {
                    p.style.display = p.dataset.panel === target ? 'block' : 'none';
                });
            });
        });

        // --- Upload tab logic ---
        const dropZone = modal.querySelector('#ptm-ip-drop');
        const fileInput = modal.querySelector('#ptm-ip-file');
        const preview = modal.querySelector('#ptm-ip-preview');
        const previewImg = modal.querySelector('#ptm-ip-preview-img');
        const fileInfo = modal.querySelector('#ptm-ip-file-info');
        const uploadBtn = modal.querySelector('#ptm-ip-upload-btn');
        const uploadError = modal.querySelector('#ptm-ip-upload-error');
        let selectedFile = null;

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--color-accent,#c5a572)';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = 'var(--color-border,#ccc)';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--color-border,#ccc)';
            if (e.dataTransfer.files.length > 0) {
                selectedFile = e.dataTransfer.files[0];
                showPreview(selectedFile);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                selectedFile = fileInput.files[0];
                showPreview(selectedFile);
            }
        });

        function showPreview(file) {
            preview.style.display = 'block';
            previewImg.src = URL.createObjectURL(file);
            const sizeMB = (file.size / 1048576).toFixed(1);
            fileInfo.textContent = `${file.name} (${sizeMB} MB)`;
            uploadError.style.display = 'none';
        }

        uploadBtn.addEventListener('click', () => {
            if (!selectedFile) {
                uploadError.textContent = 'Please select an image first.';
                uploadError.style.display = 'block';
                return;
            }

            const formData = new FormData();
            formData.append('upload', selectedFile);

            const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const headers = {};
            if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';

            fetch('/admin/images/ckeditor-upload', {
                method: 'POST',
                headers: headers,
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.uploaded && data.url) {
                    const altText = modal.querySelector('#ptm-ip-alt').value || '';
                    insertImage(editor, data.url, altText);
                    closeModal();
                } else {
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'Upload & Insert';
                    uploadError.textContent = data.error?.message || 'Upload failed.';
                    uploadError.style.display = 'block';
                }
            })
            .catch(err => {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload & Insert';
                uploadError.textContent = 'Network error: ' + err.message;
                uploadError.style.display = 'block';
            });
        });

        // --- Search tab logic ---
        const searchInput = modal.querySelector('#ptm-ip-search-input');
        const searchBtn = modal.querySelector('#ptm-ip-search-btn');
        const searchResults = modal.querySelector('#ptm-ip-search-results');

        function doSearch() {
            const q = searchInput.value.trim();
            const url = q ? `/admin/images/search?q=${encodeURIComponent(q)}` : '/admin/images/search';

            searchResults.innerHTML = '<p style="color:var(--color-text-muted,#999);font-size:0.8125rem;">Searching...</p>';

            fetch(url)
                .then(r => r.json())
                .then(images => {
                    if (images.length === 0) {
                        searchResults.innerHTML = '<p style="color:var(--color-text-muted,#999);font-size:0.8125rem;">No images found.</p>';
                        return;
                    }

                    searchResults.innerHTML = '';
                    images.forEach(img => {
                        const card = document.createElement('div');
                        card.style.cssText = 'border:1px solid var(--color-border,#ddd);border-radius:8px;overflow:hidden;text-align:center;';

                        const thumb = document.createElement('div');
                        thumb.style.cssText = 'aspect-ratio:1;background:var(--color-surface-2,#f5f5f5);display:flex;align-items:center;justify-content:center;overflow:hidden;';
                        const imgEl = document.createElement('img');
                        imgEl.src = img.url;
                        imgEl.alt = img.alt_text || img.slug;
                        imgEl.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;';
                        imgEl.loading = 'lazy';
                        thumb.appendChild(imgEl);

                        const info = document.createElement('div');
                        info.style.cssText = 'padding:0.4rem;';
                        const slug = document.createElement('p');
                        slug.style.cssText = 'font-size:0.7rem;font-family:monospace;color:var(--color-text,#333);margin:0 0 0.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
                        slug.textContent = img.slug;
                        slug.title = img.slug;
                        const useBtn = document.createElement('button');
                        useBtn.textContent = 'Use Image';
                        useBtn.style.cssText = 'width:100%;padding:0.3rem;background:var(--color-accent,#c5a572);color:var(--color-text-inv,#fff);border:none;border-radius:4px;font-size:0.7rem;font-weight:600;cursor:pointer;';
                        useBtn.addEventListener('click', () => {
                            insertImage(editor, img.url, img.alt_text || img.slug);
                            closeModal();
                        });

                        info.appendChild(slug);
                        info.appendChild(useBtn);
                        card.appendChild(thumb);
                        card.appendChild(info);
                        searchResults.appendChild(card);
                    });
                })
                .catch(err => {
                    searchResults.innerHTML = `<p style="color:var(--color-danger,#dc2626);font-size:0.8125rem;">Error: ${err.message}</p>`;
                });
        }

        searchBtn.addEventListener('click', doSearch);
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
        });

        // --- URL tab logic ---
        const urlInput = modal.querySelector('#ptm-ip-url-input');
        const urlAltInput = modal.querySelector('#ptm-ip-url-alt');
        const urlBtn = modal.querySelector('#ptm-ip-url-btn');

        urlBtn.addEventListener('click', () => {
            const url = urlInput.value.trim();
            if (!url) {
                urlInput.style.borderColor = 'var(--color-danger,#dc2626)';
                urlInput.focus();
                return;
            }
            const alt = urlAltInput.value.trim();
            insertImage(editor, url, alt);
            closeModal();
        });

        urlInput.addEventListener('input', () => {
            urlInput.style.borderColor = 'var(--color-border,#ddd)';
        });

        // Focus search input when switching to search tab
        modal.querySelector('.ptm-ip-tab[data-tab="search"]').addEventListener('click', () => {
            setTimeout(() => {
                searchInput.focus();
                if (!searchResults.hasChildNodes()) doSearch();
            }, 50);
        });
    }
}

/**
 * Insert an image into the editor at the current cursor position.
 */
function insertImage(editor, url, altText) {
    editor.model.change(writer => {
        const imageElement = writer.createElement('imageBlock', {
            src: url,
            alt: altText || ''
        });
        editor.model.insertContent(imageElement, editor.model.document.selection);
    });
}

// SVG icon — picture/image
const pickerIcon = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>';
