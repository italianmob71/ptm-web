/**
 * PdfPicker — custom CKEditor 5 plugin for PTM
 *
 * Adds a toolbar button with the Adobe PDF icon. When clicked, opens a modal
 * overlay with three tabs: Upload New, Search Library, and URL.
 *
 * Upload → posts to /admin/pdfs/ckeditor-upload, inserts a download link
 * Search → fetches /admin/pdfs/search?q=..., shows list with "Use PDF" buttons
 * URL → user pastes a URL, inserts a download link at cursor
 *
 * Cancel at any stage closes the modal. No editor changes.
 */

import { Plugin, ButtonView } from 'ckeditor5';

export default class PdfPicker extends Plugin {
    static get pluginName() {
        return 'PdfPicker';
    }

    init() {
        const editor = this.editor;
        const t = editor.t;

        editor.ui.componentFactory.add('pdfPicker', locale => {
            const view = new ButtonView(locale);

            view.set({
                label: t('Insert PDF'),
                icon: pdfIcon,
                tooltip: true
            });

            view.on('execute', () => {
                this.openModal(editor);
            });

            return view;
        });
    }

    openModal(editor) {
        const existing = document.getElementById('ptm-pdf-picker-overlay');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.id = 'ptm-pdf-picker-overlay';
        overlay.style.cssText = [
            'position:fixed', 'inset:0', 'z-index:10000',
            'background:rgba(0,0,0,0.6)', 'display:flex',
            'align-items:flex-start', 'justify-content:center',
            'padding-top:5vh', 'overflow-y:auto'
        ].join(';');

        const modal = document.createElement('div');
        modal.style.cssText = [
            'background:var(--color-surface,#fff)', 'border:1px solid var(--color-border,#ddd)',
            'border-radius:12px', 'width:90%', 'max-width:720px',
            'max-height:88vh', 'overflow-y:auto',
            'box-shadow:0 8px 32px rgba(0,0,0,0.3)', 'padding:0'
        ].join(';');

        modal.innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid var(--color-border,#ddd);position:sticky;top:0;background:var(--color-surface,#fff);z-index:1;">
                <h3 style="margin:0;font-family:var(--font-serif,serif);font-size:1.25rem;color:var(--color-text,#333);display:flex;align-items:center;gap:0.5rem;">
                    <svg width="20" height="25" viewBox="0 0 32 40" fill="none" style="flex-shrink:0;">
                        <path d="M4 0a4 4 0 0 0-4 4v32a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V12L20 0H4z" fill="#E9352F"/>
                        <path d="M20 0v8a4 4 0 0 0 4 4h8L20 0z" fill="#b3251e"/>
                        <text x="16" y="29" text-anchor="middle" fill="white" font-family="Helvetica,Arial,sans-serif" font-size="8" font-weight="700" letter-spacing="0.5">PDF</text>
                    </svg>
                    Insert PDF
                </h3>
                <button id="ptm-pp-close" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--color-text-muted,#999);padding:0 0.5rem;line-height:1;">×</button>
            </div>

            <div style="display:flex;border-bottom:1px solid var(--color-border,#ddd);position:sticky;top:3.5rem;background:var(--color-surface,#fff);z-index:1;">
                <button class="ptm-pp-tab" data-tab="upload" style="flex:1;padding:0.75rem;background:none;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;border-bottom:3px solid var(--color-accent,#c5a572);color:var(--color-text,#333);">Upload New</button>
                <button class="ptm-pp-tab" data-tab="search" style="flex:1;padding:0.75rem;background:none;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;border-bottom:3px solid transparent;color:var(--color-text-muted,#999);">Search Library</button>
                <button class="ptm-pp-tab" data-tab="url" style="flex:1;padding:0.75rem;background:none;border:none;cursor:pointer;font-size:0.875rem;font-weight:600;border-bottom:3px solid transparent;color:var(--color-text-muted,#999);">URL</button>
            </div>

            <div style="padding:1.5rem;">
                <!-- Upload Tab -->
                <div class="ptm-pp-panel" data-panel="upload">
                    <div id="ptm-pp-drop" style="border:2px dashed var(--color-border,#ccc);border-radius:8px;padding:2rem;text-align:center;cursor:pointer;transition:border-color 0.2s;">
                        <p style="color:var(--color-text-muted,#999);font-size:0.875rem;margin:0;">Click or drag a PDF here to upload</p>
                        <p style="color:var(--color-text-faint,#ccc);font-size:0.75rem;margin-top:0.5rem;">PDF files only — max 50MB</p>
                        <input type="file" id="ptm-pp-file" accept="application/pdf,.pdf" style="display:none;">
                    </div>
                    <div id="ptm-pp-preview" style="margin-top:1rem;display:none;">
                        <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;background:var(--color-surface-2,#f5f5f5);">
                            <svg width="24" height="30" viewBox="0 0 32 40" fill="none" style="flex-shrink:0;">
                                <path d="M4 0a4 4 0 0 0-4 4v32a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V12L20 0H4z" fill="#E9352F"/>
                                <path d="M20 0v8a4 4 0 0 0 4 4h8L20 0z" fill="#b3251e"/>
                                <text x="16" y="29" text-anchor="middle" fill="white" font-family="Helvetica,Arial,sans-serif" font-size="8" font-weight="700">PDF</text>
                            </svg>
                            <div>
                                <p id="ptm-pp-file-name" style="margin:0;font-size:0.8125rem;font-weight:600;color:var(--color-text,#333);"></p>
                                <p id="ptm-pp-file-size" style="margin:0;font-size:0.75rem;color:var(--color-text-muted,#999);"></p>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:1rem;">
                        <input type="text" id="ptm-pp-label" placeholder="Link text (optional — defaults to filename)" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                    </div>
                    <div style="display:flex;gap:0.75rem;margin-top:1rem;">
                        <button id="ptm-pp-upload-btn" style="padding:0.5rem 1.5rem;background:var(--color-accent,#c5a572);color:var(--color-text-inv,#fff);border:none;border-radius:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">Upload & Insert</button>
                        <button id="ptm-pp-cancel-upload" style="padding:0.5rem 1.5rem;background:none;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;cursor:pointer;color:var(--color-text-muted,#999);">Cancel</button>
                    </div>
                    <div id="ptm-pp-upload-error" style="margin-top:0.75rem;color:var(--color-danger,#dc2626);font-size:0.75rem;display:none;"></div>
                </div>

                <!-- Search Tab -->
                <div class="ptm-pp-panel" data-panel="search" style="display:none;">
                    <div style="display:flex;gap:0.5rem;margin-bottom:1rem;">
                        <input type="text" id="ptm-pp-search-input" placeholder="Search PDFs by name, title, or category..." style="flex:1;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                        <button id="ptm-pp-search-btn" style="padding:0.5rem 1rem;background:none;border:1px solid var(--color-accent,#c5a572);color:var(--color-accent,#c5a572);border-radius:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">Search</button>
                    </div>
                    <div id="ptm-pp-search-results" style="display:flex;flex-direction:column;gap:0.5rem;max-height:400px;overflow-y:auto;"></div>
                </div>

                <!-- URL Tab -->
                <div class="ptm-pp-panel" data-panel="url" style="display:none;">
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:0.8125rem;color:var(--color-text,#333);margin-bottom:0.5rem;">PDF URL</label>
                        <input type="text" id="ptm-pp-url-input" placeholder="https://example.com/document.pdf" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:0.8125rem;color:var(--color-text,#333);margin-bottom:0.5rem;">Link text (optional)</label>
                        <input type="text" id="ptm-pp-url-label" placeholder="Download PDF" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;background:var(--color-surface,#fff);color:var(--color-text,#333);">
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <button id="ptm-pp-url-btn" style="padding:0.5rem 1.5rem;background:var(--color-accent,#c5a572);color:var(--color-text-inv,#fff);border:none;border-radius:6px;font-size:0.8125rem;font-weight:600;cursor:pointer;">Insert PDF Link</button>
                        <button id="ptm-pp-cancel-url" style="padding:0.5rem 1.5rem;background:none;border:1px solid var(--color-border,#ddd);border-radius:6px;font-size:0.8125rem;cursor:pointer;color:var(--color-text-muted,#999);">Cancel</button>
                    </div>
                </div>
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        const closeModal = () => overlay.remove();

        modal.querySelector('#ptm-pp-close').addEventListener('click', closeModal);
        modal.querySelector('#ptm-pp-cancel-upload').addEventListener('click', closeModal);
        modal.querySelector('#ptm-pp-cancel-url').addEventListener('click', closeModal);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });

        // Tab switching
        modal.querySelectorAll('.ptm-pp-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                modal.querySelectorAll('.ptm-pp-tab').forEach(t => {
                    t.style.borderBottomColor = 'transparent';
                    t.style.color = 'var(--color-text-muted,#999)';
                });
                tab.style.borderBottomColor = 'var(--color-accent,#c5a572)';
                tab.style.color = 'var(--color-text,#333)';
                modal.querySelectorAll('.ptm-pp-panel').forEach(p => {
                    p.style.display = p.dataset.panel === target ? 'block' : 'none';
                });
            });
        });

        // --- Upload tab ---
        const dropZone = modal.querySelector('#ptm-pp-drop');
        const fileInput = modal.querySelector('#ptm-pp-file');
        const preview = modal.querySelector('#ptm-pp-preview');
        const fileName = modal.querySelector('#ptm-pp-file-name');
        const fileSizeEl = modal.querySelector('#ptm-pp-file-size');
        const uploadBtn = modal.querySelector('#ptm-pp-upload-btn');
        const uploadError = modal.querySelector('#ptm-pp-upload-error');
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
            fileName.textContent = file.name;
            const sizeMB = (file.size / 1048576).toFixed(1);
            fileSizeEl.textContent = sizeMB + ' MB';
            uploadError.style.display = 'none';
        }

        uploadBtn.addEventListener('click', () => {
            if (!selectedFile) {
                uploadError.textContent = 'Please select a PDF first.';
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

            fetch('/admin/pdfs/ckeditor-upload', {
                method: 'POST',
                headers: headers,
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.uploaded && data.url) {
                    const label = modal.querySelector('#ptm-pp-label').value || data.filename || 'Download PDF';
                    insertPdfLink(editor, data.url, label);
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

        // --- Search tab ---
        const searchInput = modal.querySelector('#ptm-pp-search-input');
        const searchBtn = modal.querySelector('#ptm-pp-search-btn');
        const searchResults = modal.querySelector('#ptm-pp-search-results');

        function doSearch() {
            const q = searchInput.value.trim();
            const url = q ? `/admin/pdfs/search?q=${encodeURIComponent(q)}` : '/admin/pdfs/search';

            searchResults.innerHTML = '<p style="color:var(--color-text-muted,#999);font-size:0.8125rem;">Searching...</p>';

            fetch(url)
                .then(r => r.json())
                .then(pdfs => {
                    if (pdfs.length === 0) {
                        searchResults.innerHTML = '<p style="color:var(--color-text-muted,#999);font-size:0.8125rem;">No PDFs found.</p>';
                        return;
                    }

                    searchResults.innerHTML = '';
                    pdfs.forEach(pdf => {
                        const card = document.createElement('div');
                        card.style.cssText = 'display:flex;align-items:center;gap:0.75rem;padding:0.75rem;border:1px solid var(--color-border,#ddd);border-radius:8px;background:var(--color-surface,#fff);';

                        const icon = document.createElement('div');
                        icon.style.cssText = 'flex-shrink:0;';
                        icon.innerHTML = '<svg width="24" height="30" viewBox="0 0 32 40" fill="none"><path d="M4 0a4 4 0 0 0-4 4v32a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V12L20 0H4z" fill="#E9352F"/><path d="M20 0v8a4 4 0 0 0 4 4h8L20 0z" fill="#b3251e"/><text x="16" y="29" text-anchor="middle" fill="white" font-family="Helvetica,Arial,sans-serif" font-size="8" font-weight="700">PDF</text></svg>';

                        const info = document.createElement('div');
                        info.style.cssText = 'flex:1;min-width:0;';
                        const title = document.createElement('p');
                        title.style.cssText = 'margin:0;font-size:0.8125rem;font-weight:600;color:var(--color-text,#333);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
                        title.textContent = pdf.title || pdf.filename;
                        title.title = pdf.filename;
                        const slug = document.createElement('p');
                        slug.style.cssText = 'margin:0;font-size:0.7rem;font-family:monospace;color:var(--color-text-faint,#ccc);';
                        slug.textContent = pdf.slug;
                        info.appendChild(title);
                        info.appendChild(slug);

                        const useBtn = document.createElement('button');
                        useBtn.textContent = 'Use PDF';
                        useBtn.style.cssText = 'flex-shrink:0;padding:0.4rem 0.8rem;background:var(--color-accent,#c5a572);color:var(--color-text-inv,#fff);border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;';
                        useBtn.addEventListener('click', () => {
                            insertPdfLink(editor, pdf.url, pdf.title || pdf.filename);
                            closeModal();
                        });

                        card.appendChild(icon);
                        card.appendChild(info);
                        card.appendChild(useBtn);
                        searchResults.appendChild(card);
                    });
                })
                .catch(err => {
                    searchResults.innerHTML = '<p style="color:var(--color-danger,#dc2626);font-size:0.8125rem;">Error: ' + err.message + '</p>';
                });
        }

        searchBtn.addEventListener('click', doSearch);
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
        });

        // --- URL tab ---
        const urlInput = modal.querySelector('#ptm-pp-url-input');
        const urlLabel = modal.querySelector('#ptm-pp-url-label');
        const urlBtn = modal.querySelector('#ptm-pp-url-btn');

        urlBtn.addEventListener('click', () => {
            const url = urlInput.value.trim();
            if (!url) {
                urlInput.style.borderColor = 'var(--color-danger,#dc2626)';
                urlInput.focus();
                return;
            }
            const label = urlLabel.value.trim() || 'Download PDF';
            insertPdfLink(editor, url, label);
            closeModal();
        });

        urlInput.addEventListener('input', () => {
            urlInput.style.borderColor = 'var(--color-border,#ddd)';
        });

        // Auto-load search when switching to search tab
        modal.querySelector('.ptm-pp-tab[data-tab="search"]').addEventListener('click', () => {
            setTimeout(() => {
                searchInput.focus();
                if (!searchResults.hasChildNodes()) doSearch();
            }, 50);
        });
    }
}

/**
 * Insert a PDF download link into the editor at the current cursor position.
 * Renders as a styled paragraph with the PDF icon and a clickable link.
 */
function insertPdfLink(editor, url, label) {
    const html = '<p style="display:flex;align-items:center;gap:0.5rem;">'
        + '<svg width="20" height="25" viewBox="0 0 32 40" fill="none" style="flex-shrink:0;vertical-align:middle">'
        + '<path d="M4 0a4 4 0 0 0-4 4v32a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V12L20 0H4z" fill="#E9352F"/>'
        + '<path d="M20 0v8a4 4 0 0 0 4 4h8L20 0z" fill="#b3251e"/>'
        + '<text x="16" y="29" text-anchor="middle" fill="white" font-family="Helvetica,Arial,sans-serif" font-size="8" font-weight="700">PDF</text>'
        + '</svg>'
        + '<a href="' + url + '" target="_blank" rel="noopener">' + escapeHtml(label) + '</a>'
        + '</p>';

    const viewFragment = editor.data.processor.toView(html);
    const modelFragment = editor.data.toModel(viewFragment);
    editor.model.insertContent(modelFragment, editor.model.document.selection);
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// SVG icon — Adobe PDF document
const pdfIcon = '<svg viewBox="0 0 32 40" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M4 0a4 4 0 0 0-4 4v32a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V12L20 0H4z"/><path fill="currentColor" opacity="0.3" d="M20 0v8a4 4 0 0 0 4 4h8L20 0z"/><rect x="6" y="16" width="20" height="18" rx="2" fill="#E9352F"/><text x="16" y="29" text-anchor="middle" fill="white" font-family="Helvetica,Arial,sans-serif" font-size="8" font-weight="700" letter-spacing="0.5">PDF</text></svg>';
