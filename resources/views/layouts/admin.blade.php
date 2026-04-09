{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Admin') – Kusoma CMS</title>
  <meta name="robots" content="noindex, nofollow">
<script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Serif+4:wght@0,400;0,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
   <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        display: ['Lora', 'serif'],
                    },
                    colors: {
                        navy: '#1a2c5b',
                        royal: '#2755c8',
                        mid: '#4a72e8',
                        klight: '#eef2fb',
                        kgreen: '#1a7a45',
                        korange: '#d97706',
                        kbg: '#f7f8fa',
                        kborder: '#e2e6ed',
                        muted: '#6b7280',
                        /* Category Colors */
                        ctech: '#2755c8',
                        cpol: '#1a2c5b',
                        cbiz: '#1a7a45',
                        cent: '#b45309',
                        csport: '#6d28d9',
                        cedu: '#0369a1',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-kbg font-body antialiased text-gray-800">

<div class="flex h-full min-h-screen">

  {{-- ── Sidebar ────────────────────────────────────────────────────────── --}}
  <aside class="w-56 shrink-0 bg-navy text-white flex flex-col sticky top-0 h-screen overflow-y-auto">

    {{-- Wordmark --}}
    <div class="px-5 py-4 border-b border-white/10">
      <a href="{{ route('home') }}" class="font-display font-bold text-xl tracking-tight">
        Kusoma
      </a>
      <p class="text-[11px] text-white/40 mt-0.5">Content Management</p>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5" aria-label="Admin navigation">

      <a href="{{ route('dashboard') }}"
         class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                transition-colors">
        <i class="fas fa-gauge-high w-4 text-center text-xs"></i> Dashboard
      </a>

      <div class="pt-3 pb-1 px-3">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Content</p>
      </div>

      <a href="{{ route('admin.posts.index') }}"
         class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                {{ request()->routeIs('admin.posts.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                transition-colors">
        <i class="fas fa-newspaper w-4 text-center text-xs"></i> Posts
      </a>

      <a href="{{ route('admin.categories.index') }}"
         class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                {{ request()->routeIs('admin.categories.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                transition-colors">
        <i class="fas fa-folder-open w-4 text-center text-xs"></i> Categories
      </a>

      <a href="{{ route('admin.tags.index') }}"
         class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm
                {{ request()->routeIs('admin.tags.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                transition-colors">
        <i class="fas fa-tags w-4 text-center text-xs"></i> Tags
      </a>

      <div class="pt-3 pb-1 px-3">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Community</p>
      </div>

      <a href="{{ route('admin.comments.index') }}"
         class="flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg text-sm
                {{ request()->routeIs('admin.comments.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                transition-colors">
        <span class="flex items-center gap-2.5">
          <i class="fas fa-comments w-4 text-center text-xs"></i> Comments
        </span>
        {{-- Count only pending comments (where is_approved = false) --}}
        @php 
          $pendingCount = \App\Models\Comment::where('is_approved', false)->count(); 
        @endphp
        @if($pendingCount > 0)
          <span class="text-[10px] font-bold bg-red-500 text-white rounded-full px-1.5 py-0.5 leading-none">
            {{ $pendingCount }}
          </span>
        @endif
      </a>

      {{-- Optional: Quick moderation link for pending comments only --}}
      @if($pendingCount > 0)
      <a href="{{ route('admin.comments.index', ['filter' => 'pending']) }}"
         class="flex items-center gap-2.5 pl-8 pr-3 py-1.5 rounded-lg text-xs text-white/50 hover:bg-white/10 hover:text-white/70 transition-colors">
        <i class="fas fa-clock w-3 text-center"></i> 
        <span>Pending approval ({{ $pendingCount }})</span>
      </a>
      @endif

      <div class="pt-3 pb-1 px-3">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Site</p>
      </div>

      <a href="{{ route('home') }}" target="_blank"
         class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white transition-colors">
        <i class="fas fa-arrow-up-right-from-square w-4 text-center text-xs"></i> View site
      </a>

    </nav>

    {{-- User --}}
    <div class="px-4 py-3 border-t border-white/10 flex items-center gap-2.5">
      <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=2755c8&color=fff&size=32"
           class="w-8 h-8 rounded-full shrink-0" alt="You" width="32" height="32" />
      <div class="min-w-0 flex-1">
        <p class="text-xs font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="text-[11px] text-white/40 hover:text-white transition-colors">
            Sign out
          </button>
        </form>
      </div>
    </div>

  </aside>

  {{-- ── Main area ──────────────────────────────────────────────────────── --}}
  <div class="flex-1 flex flex-col min-w-0 overflow-auto">

    {{-- Top bar --}}
    <div class="sticky top-0 z-10 bg-white border-b border-kborder px-6 py-3 flex items-center justify-between gap-4">
      <h1 class="text-base font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
      <div class="flex items-center gap-3">
        @yield('topbar_actions')
        <a href="{{ route('admin.posts.create') }}"
           class="bg-royal hover:bg-navy text-white text-sm font-medium px-4 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
          <i class="fas fa-plus text-xs"></i> New Post
        </a>
      </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 flex items-center gap-2">
      <i class="fas fa-circle-check shrink-0"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 flex items-center gap-2">
      <i class="fas fa-circle-exclamation shrink-0"></i> {{ session('error') }}
    </div>
    @endif
    @if(session('warning'))
    <div class="mx-6 mt-4 bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm rounded-lg px-4 py-3 flex items-center gap-2">
      <i class="fas fa-triangle-exclamation shrink-0"></i> {{ session('warning') }}
    </div>
    @endif

    {{-- Page content --}}
    <main class="flex-1 p-6">
      @yield('content')
    </main>

  </div>
</div>

@stack('scripts')
</body>
</html>