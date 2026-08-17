@props([
    'source'  => null,
    'slug'    => null,
    'limit'   => 15,
    'minRelevancy' => 25,
])

@php
    use App\Services\RelatedContentFinder;

    $sourceSlug = null;
    $excludeModel = null;

    if ($source) {
        $sourceSlug = $source->slug ?? null;
        $excludeModel = $source;
    } elseif ($slug) {
        $sourceSlug = $slug;
    }

    if (!$sourceSlug) {
        return;
    }

    $finder = new RelatedContentFinder();

    $exclude = [];
    if ($excludeModel) {
        $sourceClass = get_class($excludeModel);
        $exclude[$sourceClass] = [$excludeModel->id];
    }

    $grouped = $finder->findBySlugGrouped($sourceSlug, $exclude, $minRelevancy);

    if (empty($grouped)) {
        return;
    }

    $tabConfig = [
        'articles'     => ['icon' => 'icon-article',     'label' => 'Articles',     'color' => 'var(--color-accent)'],
        'cochin-books' => ['icon' => 'icon-scroll',      'label' => 'Cochin Books', 'color' => 'var(--color-accent)'],
        'books'        => ['icon' => 'icon-book',         'label' => 'Books',        'color' => 'var(--color-accent)'],
        'pdfs'         => ['icon' => 'icon-pdf',          'label' => 'PDFs',         'color' => '#E9352F'],
        'videos'       => ['icon' => 'icon-video',        'label' => 'Videos',       'color' => 'var(--color-text-muted)'],
        'images'       => ['icon' => 'icon-image',        'label' => 'Images',       'color' => 'var(--color-text-muted)'],
    ];

    $typeIcons = [
        'article'      => 'icon-article',
        'blog'         => 'icon-blog',
        'travel-note'  => 'icon-travel-note',
        'cochin-book'  => 'icon-scroll',
        'book'         => 'icon-book',
        'pdf'          => 'icon-pdf',
        'video'        => 'icon-video',
        'image'        => 'icon-image',
    ];

    $uid = 'sa' . substr(uniqid(), -6);
    $totalItems = 0;
    foreach ($grouped as $items) {
        $totalItems += $items->count();
    }
@endphp

<div id="{{ $uid }}" x-data="{ tab: '{{ array_key_first($grouped) }}', open: {} }" style="max-width:50rem;margin:2.5rem auto">
  <div style="border:1px solid var(--color-border);border-radius:var(--radius-lg,12px);background:var(--color-surface);overflow:hidden">

    {{-- Header --}}
    <div style="padding:.75rem 1.25rem;border-bottom:1px solid var(--color-border);background:var(--color-surface-2,rgba(128,128,128,.06));display:flex;align-items:center;gap:.5rem">
      <svg style="width:1.25rem;height:1.25rem;color:var(--color-accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
      </svg>
      <h2 style="font-family:var(--font-serif,Georgia,serif);font-size:1.125rem;font-weight:700;color:var(--color-text);margin:0">See Also</h2>
      <span style="margin-left:auto;font-size:.75rem;color:var(--color-text-faint)">{{ $totalItems }} related · {{ count($grouped) }} categories</span>
    </div>

    {{-- Tabs --}}
    <div class="sa-tabs" style="display:flex;overflow-x:auto;border-bottom:1px solid var(--color-border);background:var(--color-surface-2,rgba(128,128,128,.03))">
      @foreach ($grouped as $tabKey => $items)
        @php $tcfg = $tabConfig[$tabKey] ?? ['icon'=>'icon-article','label'=>ucfirst($tabKey),'color'=>'var(--color-accent)']; @endphp
        <button @click="tab='{{ $tabKey }}'"
                :style="tab==='{{ $tabKey }}' ? 'border-color:{{ $tcfg['color'] }};color:var(--color-text)' : 'border-color:transparent;color:var(--color-text-faint)'"
                class="sa-tab">
          <svg style="width:.875rem;height:.875rem" aria-hidden="true"><use href="#{{ $tcfg['icon'] }}"></use></svg>
          {{ $tcfg['label'] }}
          <span class="sa-tab-count">{{ $items->count() }}</span>
        </button>
      @endforeach
    </div>

    {{-- Column headers --}}
    <div class="sa-row sa-row-header">
      <span class="sa-col-pct">Relevancy</span>
      <span class="sa-col-bar">Match</span>
      <span class="sa-col-icon">Type</span>
      <span class="sa-col-title">Title</span>
    </div>

    {{-- Tab panels --}}
    @foreach ($grouped as $tabKey => $items)
      @php $hasMore = $items->count() > $limit; @endphp
      <div x-show="tab==='{{ $tabKey }}'" x-cloak>
        @foreach ($items as $idx => $item)
          @php
            $icon = $typeIcons[$item->type] ?? 'icon-article';
            $pct = $item->relevancy;
          @endphp
          <a href="{{ $item->url }}"
             x-show="tab==='{{ $tabKey }}' && ({{ $idx }} < {{ $limit }} || open['{{ $tabKey }}'])"
             x-cloak
             class="sa-row sa-row-item">
            <span class="sa-col-pct sa-pct-val">{{ $pct }}%</span>
            <span class="sa-col-bar"><span class="sa-bar-fill" style="width:{{ $pct }}%"></span></span>
            <span class="sa-col-icon"><svg style="width:1.25rem;height:1.25rem;color:var(--color-text-faint);display:block" aria-hidden="true"><use href="#{{ $icon }}"></use></svg></span>
            <span class="sa-col-title">
              <span class="sa-title-text">{{ $item->title }}</span>
              <span class="sa-type-label">{{ $item->type_label }}</span>
            </span>
          </a>
        @endforeach

        @if ($hasMore)
        <button @click="open['{{ $tabKey }}'] = !open['{{ $tabKey }}']"
                x-show="tab==='{{ $tabKey }}'"
                x-cloak
                class="sa-see-more">
          <span x-show="!open['{{ $tabKey }}']">See {{ $items->count() - $limit }} more ↓</span>
          <span x-show="open['{{ $tabKey }}']" x-cloak>Show less ↑</span>
        </button>
        @endif
      </div>
    @endforeach
  </div>
</div>

<style>
[x-cloak]{display:none!important;}
.sa-tab{
  display:flex;align-items:center;gap:.375rem;
  padding:.5rem .875rem;font-size:.8125rem;font-weight:500;
  white-space:nowrap;border:none;border-bottom:2px solid transparent;
  cursor:pointer;transition:all .15s ease;background:transparent;
}
.sa-tab-count{
  font-size:.625rem;padding:.0625rem .3125rem;border-radius:.625rem;
  background:rgba(128,128,128,.08);color:var(--color-text-faint);
}
/* Grid row: relevancy | bar | icon | title */
.sa-row{
  display:grid;
  grid-template-columns:5rem 4rem 1.75rem 1fr;
  gap:.75rem;align-items:center;
  padding:.75rem 1.25rem;
}
.sa-row-header{
  border-bottom:1px solid var(--color-border);
  background:var(--color-surface-2,rgba(128,128,128,.03));
}
.sa-row-header span{
  font-size:.6875rem;font-weight:600;text-transform:uppercase;
  letter-spacing:.08em;color:var(--color-text-faint);
}
.sa-row-header .sa-col-pct{text-align:right;}
.sa-row-item{
  text-decoration:none;color:var(--color-text);
  border-bottom:1px solid var(--color-border);
  transition:background .15s ease;
}
.sa-row-item:hover{
  background:var(--color-surface-2,rgba(128,128,128,.06));
}
.sa-col-pct{
  text-align:right;font-size:.8125rem;font-weight:600;
  color:var(--color-text-faint);font-variant-numeric:tabular-nums;
}
.sa-col-bar{
  height:4px;border-radius:2px;
  background:rgba(128,128,128,.1);overflow:hidden;position:relative;
}
.sa-bar-fill{
  display:block;height:100%;border-radius:2px;
  background:var(--color-accent);transition:width .3s ease;
}
.sa-col-title{min-width:0;}
.sa-title-text{
  display:block;font-size:.875rem;font-weight:500;color:var(--color-text);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.sa-type-label{
  display:block;font-size:.6875rem;color:var(--color-text-faint);
  text-transform:uppercase;letter-spacing:.05em;margin-top:1px;
}
.sa-see-more{
  display:block;width:100%;padding:.625rem;font-size:.8125rem;
  font-weight:500;color:var(--color-accent);background:transparent;
  border:none;border-bottom:1px solid var(--color-border);cursor:pointer;
  transition:background .15s ease;
}
.sa-see-more:hover{background:var(--color-surface-2,rgba(128,128,128,.04));}
</style>
