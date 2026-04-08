{{-- resources/views/components/ui/marketing/nav.blade.php --}}
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-kborder">
  <div class="max-w-6xl mx-auto px-6">
    <div class="flex items-center justify-between h-14 gap-4">

      {{-- Wordmark --}}
      <a href="{{ route('news.index') }}" class="font-display font-bold text-2xl text-navy tracking-tight shrink-0">
        Kusoma
      </a>

      {{-- Category nav (desktop) --}}
      <nav class="hidden md:flex items-center gap-1 text-sm font-medium overflow-x-auto" aria-label="Categories">
        <a href="{{ route('news.index') }}"
           class="px-3 py-1.5 rounded-lg text-muted hover:text-navy hover:bg-kbg transition-colors whitespace-nowrap
                  {{ request()->routeIs('news.index') ? 'text-navy bg-kbg' : '' }}">
          Home
        </a>
        @foreach(\App\Models\Category::all() as $cat)
        <a href="{{ route('category.show', $cat->slug) }}"
           class="px-3 py-1.5 rounded-lg text-muted hover:text-navy hover:bg-kbg transition-colors whitespace-nowrap
                  {{ request()->routeIs('category.show') && request()->route('slug') === $cat->slug ? 'text-navy bg-kbg' : '' }}">
          {{ $cat->name }}
        </a>
        @endforeach
      </nav>

      {{-- Right: search + RSS --}}
      <div class="flex items-center gap-2 shrink-0">
        <a href="{{ route('feed.rss') }}" title="RSS Feed"
           class="text-muted hover:text-royal p-2 rounded-lg hover:bg-kbg transition-colors hidden sm:block">
          <i class="fas fa-rss text-sm"></i>
        </a>
        <button aria-label="Search"
                class="flex items-center gap-2 text-sm text-muted border border-kborder rounded-lg px-3 py-1.5 hover:border-royal hover:text-royal transition-colors">
          <i class="fas fa-search text-xs"></i>
          <span class="hidden sm:inline">Search</span>
        </button>
      </div>

    </div>
  </div>

  {{-- Mobile category scroll --}}
  <div class="md:hidden border-t border-kborder overflow-x-auto">
    <div class="flex gap-1 px-4 py-2">
      @foreach(\App\Models\Category::all() as $cat)
      <a href="{{ route('category.show', $cat->slug) }}"
         class="text-xs font-medium px-3 py-1 rounded-full border border-kborder text-muted hover:bg-kbg whitespace-nowrap transition-colors">
        {{ $cat->name }}
      </a>
      @endforeach
    </div>
  </div>
</header>
