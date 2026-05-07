@extends('layouts.admin')

@section('content')
<div class="px-6 lg:px-8 py-6 space-y-5">

    {{-- ── PAGE HEADER ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-display font-semibold text-2xl text-ink leading-tight">Articles</h1>
            <p class="text-sm text-muted mt-0.5">Manage, publish, and organise your editorial content</p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('dashboard.posts.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-semibold transition-all"
               style="background:#E8372C; box-shadow:0 4px 14px rgba(232,55,44,.32);"
               onmouseenter="this.style.background='#C0211A'"
               onmouseleave="this.style.background='#E8372C'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New Article
            </a>
        </div>
    </div>

    {{-- ── SUCCESS ALERT ── --}}
    @if(session('success'))
        <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl text-sm font-medium"
             style="background:#DCFCE7; color:#15803D; border:1px solid #BBF7D0;">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── STATS STRIP ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $allPosts   = $posts->total() ?? count($posts);
            $published  = isset($posts) ? $posts->where('status', 'published')->count() : 0;
            $drafts     = isset($posts) ? $posts->where('status', 'draft')->count() : 0;
            $breaking   = isset($posts) ? $posts->where('status', 'breaking')->count() : 0;
            $scheduled  = isset($posts) ? $posts->where('status', 'scheduled')->count() : 0;
        @endphp
        @foreach([
            ['label'=>'Total Articles',  'val'=> $allPosts,  'ico'=>'fa-newspaper',     'col'=>'#E8372C'],
            ['label'=>'Published',        'val'=> $published, 'ico'=>'fa-circle-check',  'col'=>'#16A34A'],
            ['label'=>'Drafts',           'val'=> $drafts,    'ico'=>'fa-file-pen',      'col'=>'#D97706'],
            ['label'=>'Breaking',         'val'=> $breaking,  'ico'=>'fa-bolt',          'col'=>'#7C3AED'],
        ] as $s)
            <div class="bg-white rounded-xl bborder border-kborder px-4 py-3.5" style="box-shadow:0 1px 3px rgba(0,0,0,.05)">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold uppercase tracking-widest text-muted">{{ $s['label'] }}</span>
                    <i class="fas {{ $s['ico'] }} text-sm" style="color:{{ $s['col'] }};opacity:.7"></i>
                </div>
                <p class="text-2xl font-bold text-ink font-display">{{ number_format($s['val']) }}</p>
            </div>
        @endforeach
    </div>

    {{-- ── FILTER / SEARCH BAR ── --}}
    <div class="bg-white rounded-xl border border-kborder px-4 py-3 flex flex-col sm:flex-row gap-3 items-start sm:items-center"
         style="box-shadow:0 1px 3px rgba(0,0,0,.05)">

        {{-- Search --}}
        <div class="relative flex-1 min-w-0 max-w-xs">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-muted text-xs pointer-events-none"></i>
            <input type="text" id="searchInput" placeholder="Search articles…"
                   class="w-full pl-8 pr-3 py-2 text-sm rounded-lg outline-none transition-all"
                   style="background:#F4F5F7; border:1px solid #E1E4EB;"
                   onfocus="this.style.borderColor='#E8372C'; this.style.background='#fff';"
                   onblur="this.style.borderColor='#E1E4EB'; this.style.background='#F4F5F7';">
        </div>

        {{-- Status pills --}}
        <div class="flex items-center gap-1.5 flex-wrap">
            @foreach(['All','Published','Draft','Breaking','Scheduled'] as $filter)
                <button onclick="filterStatus(this, '{{ strtolower($filter) }}')"
                        class="status-filter text-xs font-semibold px-3 py-1.5 rounded-lg border transition-all cursor-pointer"
                        style="{{ $filter === 'All' ? 'background:#E8372C;color:#fff;border-color:#E8372C' : 'background:#F4F5F7;color:#6B7280;border-color:#E1E4EB' }}"
                        data-filter="{{ strtolower($filter) }}">
                    {{ $filter }}
                </button>
            @endforeach
        </div>

        <div class="sm:ml-auto flex items-center gap-2 text-xs text-muted shrink-0">
            <i class="fas fa-sort-amount-down text-xs"></i>
            <select id="sortSelect" class="text-xs bg-transparent outline-none border-none cursor-pointer text-muted">
                <option value="newest">Newest first</option>
                <option value="oldest">Oldest first</option>
                <option value="views">Most views</option>
            </select>
        </div>
    </div>

    {{-- ── ARTICLES TABLE ── --}}
    <div class="bg-white rounded-xl border border-kborder overflow-hidden" style="box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <table class="min-w-full">
            <thead>
                <tr style="border-bottom:2px solid #F0F2F5; background:#FAFBFC;">
                    @foreach(['Article','Category','Tags','Status','Views','Actions'] as $col)
                        <th class="px-5 py-3 text-left text-[10.5px] font-bold uppercase tracking-wider text-muted">
                            {{ $col }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="articlesTableBody">
                @forelse($posts as $post)
                    @php
                        $statusMap = [
                            'published' => ['bg'=>'#DCFCE7','color'=>'#15803D','label'=>'Published'],
                            'draft'     => ['bg'=>'#F1F5F9','color'=>'#475569','label'=>'Draft'],
                            'breaking'  => ['bg'=>'#FEE2E2','color'=>'#B91C1C','label'=>'Breaking'],
                            'scheduled' => ['bg'=>'#FEF3C7','color'=>'#B45309','label'=>'Scheduled'],
                        ];
                        $s = $statusMap[$post->status] ?? ['bg'=>'#F1F5F9','color'=>'#6B7280','label'=>ucfirst($post->status)];
                    @endphp
                    <tr class="article-row border-b border-gray-50 transition-colors"
                        style="cursor:default"
                        data-status="{{ $post->status }}"
                        data-title="{{ strtolower($post->title) }}"
                        data-views="{{ $post->view_count }}"
                        data-date="{{ $post->created_at->timestamp }}"
                        onmouseenter="this.style.background='#FAFBFC'"
                        onmouseleave="this.style.background='#fff'">

                        {{-- Title + date --}}
                        <td class="px-5 py-3.5">
                            <div class="text-[13.5px] font-semibold text-ink leading-tight line-clamp-1 max-w-xs">
                                {{ $post->title }}
                            </div>
                            <div class="text-[11px] text-muted mt-0.5 flex items-center gap-1.5">
                                <i class="fas fa-calendar-alt text-[9px]"></i>
                                {{ $post->created_at->format('M d, Y') }}
                                @if($post->author)
                                    <span class="text-border">·</span>
                                    <i class="fas fa-user-pen text-[9px]"></i>
                                    {{ $post->author->name }}
                                @endif
                            </div>
                        </td>

                        {{-- Category --}}
                        <td class="px-5 py-3.5">
                            @if($post->category)
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-0.5 rounded-md"
                                      style="background:#EFF6FF; color:#1D4ED8;">
                                    {{ $post->category->name }}
                                </span>
                            @else
                                <span class="text-xs text-muted italic">—</span>
                            @endif
                        </td>

                        {{-- Tags --}}
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap gap-1">
                                @forelse($post->tags->take(3) as $tag)
                                    <span class="text-[10.5px] font-medium px-2 py-0.5 rounded-md"
                                          style="background:#F1F5F9; color:#475569;">
                                        #{{ $tag->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-muted italic">No tags</span>
                                @endforelse
                                @if($post->tags->count() > 3)
                                    <span class="text-[10.5px] font-medium px-2 py-0.5 rounded-md"
                                          style="background:#F1F5F9; color:#94A3B8;">
                                        +{{ $post->tags->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full"
                                  style="background:{{ $s['bg'] }}; color:{{ $s['color'] }}">
                                @if($post->status === 'breaking')
                                    <span class="w-1.5 h-1.5 rounded-full bg-current pulse"></span>
                                @endif
                                {{ $s['label'] }}
                            </span>
                        </td>

                        {{-- Views --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-1.5 text-sm text-ink font-medium">
                                <i class="fas fa-eye text-muted text-xs"></i>
                                {{ number_format($post->view_count) }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                {{-- Preview --}}
                                @if($post->status === 'published')
                                    <a href="{{ route('dashboard.posts.show', $post) }}" target="_blank"
                                       class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors text-muted hover:text-blue-600"
                                       style="background:#F4F5F7"
                                       title="View live">
                                        <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                    </a>
                                @endif
                                {{-- Edit --}}
                                <a href="{{ route('dashboard.posts.edit', $post) }}"
                                   class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors text-muted hover:text-ink"
                                   style="background:#F4F5F7"
                                   title="Edit">
                                    <i class="fas fa-pen text-[11px]"></i>
                                </a>
                                {{-- Delete --}}
                                <form action="{{ route('dashboard.posts.destroy', $post) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete «{{ addslashes($post->title) }}»? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors text-muted hover:text-red-600"
                                            style="background:#F4F5F7"
                                            title="Delete">
                                        <i class="fas fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <i class="fas fa-newspaper text-gray-200 text-4xl mb-3 block"></i>
                            <p class="text-sm font-semibold text-muted">No articles yet</p>
                            <p class="text-xs text-muted mt-1 mb-4">Publish your first story to get started</p>
                            <a href="{{ route('dashboard.posts.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-semibold"
                               style="background:#E8372C;">
                                <i class="fas fa-plus text-xs"></i> Write first article
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── PAGINATION ── --}}
        @if($posts->hasPages())
            <div class="px-5 py-3.5 flex items-center justify-between"
                 style="border-top:1px solid #F0F2F5; background:#FAFBFC;">
                <p class="text-xs text-muted">
                    Showing {{ $posts->firstItem() }}–{{ $posts->lastItem() }}
                    of {{ number_format($posts->total()) }} articles
                </p>
                <div class="flex items-center gap-1">
                    {{-- Prev --}}
                    @if($posts->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs text-muted bg-gray-100 cursor-not-allowed">← Prev</span>
                    @else
                        <a href="{{ $posts->previousPageUrl() }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-medium text-ink hover:bg-gray-100 transition-colors">
                            ← Prev
                        </a>
                    @endif
                    {{-- Page numbers --}}
                    @foreach($posts->getUrlRange(max(1,$posts->currentPage()-2), min($posts->lastPage(),$posts->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}"
                           class="w-7 h-7 flex items-center justify-center rounded-lg text-xs font-medium transition-colors"
                           style="{{ $page == $posts->currentPage() ? 'background:#E8372C; color:#fff' : 'color:#374151' }}"
                           onmouseenter="{{ $page != $posts->currentPage() ? "this.style.background='#F4F5F7'" : '' }}"
                           onmouseleave="{{ $page != $posts->currentPage() ? "this.style.background=''" : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach
                    {{-- Next --}}
                    @if($posts->hasMorePages())
                        <a href="{{ $posts->nextPageUrl() }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-medium text-ink hover:bg-gray-100 transition-colors">
                            Next →
                        </a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs text-muted bg-gray-100 cursor-not-allowed">Next →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Store original order of rows
let originalRows = [];

function filterStatus(btn, filter) {
    document.querySelectorAll('.status-filter').forEach(b => {
        b.style.background = '#F4F5F7';
        b.style.color      = '#6B7280';
        b.style.borderColor= '#E1E4EB';
    });
    btn.style.background  = '#E8372C';
    btn.style.color       = '#fff';
    btn.style.borderColor = '#E8372C';

    applyFilters();
}

function applyFilters() {
    const activeFilter = document.querySelector('.status-filter[style*="background: #E8372C"]')?.getAttribute('data-filter') || 'all';
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    
    document.querySelectorAll('.article-row').forEach(row => {
        const status = row.getAttribute('data-status') || '';
        const title = row.getAttribute('data-title') || '';
        
        const matchesStatus = (activeFilter === 'all' || status === activeFilter);
        const matchesSearch = title.includes(searchTerm);
        
        row.style.display = (matchesStatus && matchesSearch) ? '' : 'none';
    });
}

// Search functionality
if(document.getElementById('searchInput')) {
    document.getElementById('searchInput').addEventListener('keyup', function() {
        applyFilters();
    });
}

// Sort functionality
if(document.getElementById('sortSelect')) {
    // Store original order on page load
    const tbody = document.getElementById('articlesTableBody');
    if(tbody) {
        originalRows = Array.from(tbody.querySelectorAll('.article-row'));
        
        document.getElementById('sortSelect').addEventListener('change', function() {
            const sortValue = this.value;
            const visibleRows = Array.from(tbody.querySelectorAll('.article-row')).filter(row => row.style.display !== 'none');
            
            visibleRows.sort((a, b) => {
                if(sortValue === 'newest') {
                    return parseInt(b.getAttribute('data-date')) - parseInt(a.getAttribute('data-date'));
                } else if(sortValue === 'oldest') {
                    return parseInt(a.getAttribute('data-date')) - parseInt(b.getAttribute('data-date'));
                } else if(sortValue === 'views') {
                    return parseInt(b.getAttribute('data-views')) - parseInt(a.getAttribute('data-views'));
                }
                return 0;
            });
            
            // Reorder the rows
            visibleRows.forEach(row => tbody.appendChild(row));
        });
    }
}

// Initialize pulse animation for breaking news
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    .pulse {
        animation: pulse 1.5s ease-in-out infinite;
    }
`;
document.head.appendChild(style);
</script>
@endpush

@endsection

{{-- ── DASHBOARD WIDGETS (from dashboard.blade.php) ── --}}