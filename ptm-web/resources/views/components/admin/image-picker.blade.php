{{-- Reusable Image Picker Component --}}
{{-- Usage: <x-admin.image-picker name="image_field" value="$model->image_field" label="Image (Front)" /> --}}

@props([
    'name',
    'value' => '',
    'label' => 'Image',
    'help' => 'Click "Select" to pick from library, or paste a URL.',
    'placeholder' => 'filename.jpg or full URL',
])

<div class="mb-4" x-data="imagePicker('{{ $name }}')" x-init="init()">
    <label class="block text-sm font-medium mb-1" style="color: var(--color-text);">{{ $label }}</label>
    
    <div class="flex gap-2">
        <input type="text"
               name="{{ $name }}"
               x-ref="input"
               :value="value"
               @input="value = $event.target.value"
               class="flex-1 px-3 py-2 rounded-lg border text-sm"
               style="border-color: var(--color-border); background-color: var(--color-surface); color: var(--color-text);"
               placeholder="{{ $placeholder }}">
        
        <button type="button"
                class="px-4 py-2 rounded-lg border text-sm font-medium whitespace-nowrap"
                style="border-color: var(--color-accent); color: var(--color-accent); background: transparent;"
                @click="openModal()">
            Select
        </button>
    </div>

    <p class="text-xs mt-1" style="color: var(--color-text-muted);">{{ $help }}</p>

    {{-- Preview --}}
    <div x-show="previewUrl" class="mt-2" x-cloak>
        <template x-if="isExternalUrl(previewUrl)">
            <img :src="previewUrl" alt="Preview" class="max-h-32 rounded border" style="border-color: var(--color-border);">
        </template>
        <template x-if="!isExternalUrl(previewUrl)">
            <img :src="asset(previewUrl)" alt="Preview" class="max-h-32 rounded border" style="border-color: var(--color-border);">
        </template>
    </div>

    {{-- Modal --}}
    <div x-show="modalOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="closeModal()"
         @keydown.escape.window="closeModal()"
         x-cloak>
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative w-full max-w-4xl max-h-[80vh] flex flex-col"
             style="background-color: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: 0 20px 50px rgba(0,0,0,0.4);">
            
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b" style="border-color: var(--color-border);">
                <h3 class="font-semibold" style="color: var(--color-text);">Select Image</h3>
                <button type="button" @click="closeModal()" class="text-2xl leading-none" style="color: var(--color-text-muted);">&times;</button>
            </div>

            <!-- Search/Filter -->
            <div class="p-4 border-b flex flex-wrap gap-3" style="border-color: var(--color-border);">
                <input type="text"
                       x-model="searchQuery"
                       @keydown.enter="searchImages()"
                       placeholder="Search images..."
                       class="flex-1 min-w-[12rem] h-9 px-3 border rounded-lg text-sm"
                       style="border-color: var(--color-border); background-color: var(--color-bg); color: var(--color-text);">
                <select x-model="categoryFilter"
                        @change="searchImages()"
                        class="h-9 px-3 border rounded-lg text-sm"
                        style="border-color: var(--color-border); background-color: var(--color-bg); color: var(--color-text);">
                    <option value="all">All categories</option>
                    <template x-for="cat in categories" :key="cat">
                        <option :value="cat" x-text="cat"></option>
                    </template>
                </select>
                <button type="button" @click="searchImages()" class="h-9 px-4 border rounded-lg text-sm font-semibold"
                        style="border-color: var(--color-accent); color: var(--color-accent); background: transparent;">
                    Search
                </button>
                <button type="button" @click="clearFilters()" class="h-9 px-4 border rounded-lg text-sm"
                        style="border-color: var(--color-border); color: var(--color-text-muted); background: transparent;">
                    Clear
                </button>
            </div>

            <!-- Results Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <template x-if="loading">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <template x-for="i in 12" :key="i">
                            <div class="animate-pulse" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden;">
                                <div style="aspect-ratio: 1; background: var(--color-surface-2);"></div>
                                <div style="padding: 0.5rem;">
                                    <div class="h-4 w-3/4" style="background: var(--color-surface-3); border-radius: 4px;"></div>
                                    <div class="h-3 w-1/2 mt-1" style="background: var(--color-surface-3); border-radius: 4px;"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!loading && images.length === 0">
                    <p class="text-center py-8" style="color: var(--color-text-muted);">No images found.</p>
                </template>

                <template x-if="!loading && images.length > 0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <template x-for="img in images" :key="img.slug">
                            <div class="image-card cursor-pointer"
                                 :class="{ 'ring-2': selectedSlug === img.slug }"
                                 style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden; transition: border-color 0.15s;"
                                 :style="selectedSlug === img.slug ? 'border-color: var(--color-accent); box-shadow: 0 0 0 2px var(--color-accent);' : ''"
                                 @click="selectImage(img)">
                                <div class="image-card__thumb" style="aspect-ratio: 1; background: var(--color-surface-2); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img :src="img.url" :alt="img.alt_text || img.slug"
                                         style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                         loading="lazy">
                                </div>
                                <div class="image-card__info" style="padding: 0.5rem;">
                                    <p class="text-xs font-mono truncate" style="color: var(--color-text);" :title="img.slug" x-text="img.slug"></p>
                                    <p class="text-xs" style="color: var(--color-text-muted);" x-text="img.file_size_human + (img.width ? ' · ' + img.width + '×' + img.height : '')"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && pagination.total > pagination.per_page" class="p-4 border-t flex items-center justify-center gap-2" style="border-color: var(--color-border);">
                <button type="button" @click="goToPage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1" class="px-3 py-1 border rounded text-sm" style="border-color: var(--color-border); color: var(--color-text);">Prev</button>
                <span class="text-sm" style="color: var(--color-text-muted);" x-text="'Page ' + pagination.current_page + ' of ' + pagination.last_page"></span>
                <button type="button" @click="goToPage(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page" class="px-3 py-1 border rounded text-sm" style="border-color: var(--color-border); color: var(--color-text);">Next</button>
            </div>

            <!-- Footer Actions -->
            <div class="p-4 border-t flex justify-end gap-3" style="border-color: var(--color-border);">
                <button type="button" @click="closeModal()" class="px-4 py-2 border rounded-lg text-sm" style="border-color: var(--color-border); color: var(--color-text);">Cancel</button>
                <button type="button" @click="confirmSelection()" :disabled="!selectedSlug" class="px-4 py-2 rounded-lg text-sm font-medium" style="background-color: var(--color-accent); color: var(--color-text-inv);">Use Selected</button>
            </div>
        </div>
    </div>
</div>

<script>
function imagePicker(fieldName) {
    return {
        value: '{{ $value }}',
        previewUrl: '',
        modalOpen: false,
        loading: false,
        searchQuery: '',
        categoryFilter: 'all',
        categories: [],
        images: [],
        selectedSlug: null,
        pagination: { current_page: 1, last_page: 1, total: 0, per_page: 24 },

        init() {
            this.updatePreview();
            this.loadCategories();
        },

        async loadCategories() {
            try {
                const res = await fetch('{{ route("admin.images.index") }}?q=&category=all', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const selects = doc.querySelectorAll('select[name="category"] option');
                this.categories = Array.from(selects).map(o => o.value).filter(v => v !== 'all');
            } catch (e) {
                console.warn('Could not load categories:', e);
            }
        },

        openModal() {
            this.modalOpen = true;
            this.selectedSlug = this.value ? this.extractSlug(this.value) : null;
            this.searchImages();
        },

        closeModal() {
            this.modalOpen = false;
        },

        extractSlug(val) {
            if (val.startsWith('http://') || val.startsWith('https://')) return val;
            const parts = val.split('/');
            return parts[parts.length - 1]?.split('.')[0] || null;
        },

        async searchImages(page = 1) {
            this.loading = true;
            this.pagination.current_page = page;
            const params = new URLSearchParams({
                q: this.searchQuery,
                category: this.categoryFilter,
                page: page,
                per_page: 24
            });
            try {
                const res = await fetch(`{{ route("admin.images.search") }}?${params}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.images = data.images || data;
                this.pagination = data.pagination || { current_page: 1, last_page: 1, total: 0, per_page: 24 };
            } catch (e) {
                console.error('Search failed:', e);
                this.images = [];
            } finally {
                this.loading = false;
            }
        },

        goToPage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.searchImages(page);
            }
        },

        clearFilters() {
            this.searchQuery = '';
            this.categoryFilter = 'all';
            this.searchImages(1);
        },

        selectImage(img) {
            this.selectedSlug = img.slug;
        },

        confirmSelection() {
            if (!this.selectedSlug) return;
            const img = this.images.find(i => i.slug === this.selectedSlug);
            if (!img) return;
            
            this.value = img.path;
            this.updatePreview();
            
            const input = this.$refs.input;
            if (input) input.value = this.value;
            
            this.closeModal();
        },

        updatePreview() {
            if (this.value) {
                this.previewUrl = this.isExternalUrl(this.value) ? this.value : this.value;
            } else {
                this.previewUrl = null;
            }
        },

        isExternalUrl(val) {
            return val.startsWith('http://') || val.startsWith('https://');
        },

        asset(path) {
            return '{{ asset("") }}' + path;
        }
    }
}
</script>

<style>
/* Skeleton shimmer */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.animate-pulse { animation: pulse 1.5s ease-in-out infinite; }

/* Selected image card */
.image-card.ring-2 {
    border-color: var(--color-accent) !important;
    box-shadow: 0 0 0 2px var(--color-accent) !important;
}
</style>