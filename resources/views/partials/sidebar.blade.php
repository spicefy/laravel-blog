{{--
    partials/sidebar.blade.php — Newsroom CMS
    ─────────────────────────────────────────
    Expected vars (injected by layouts/dashboard.blade.php):
        $activeSection  string
        $nav            array
        $adminName      string
        $adminEmail     string
        $adminInitial   string
        $adminRole      string
--}}

<aside id="sidebar"
    class="fixed lg:relative inset-y-0 left-0 z-40 w-60 flex flex-col -translate-x-full lg:translate-x-0 overflow-hidden"
    style="background:#0F1117; border-right:1px solid rgba(255,255,255,.06);">

    {{-- ── Logo / masthead ── --}}
    <div class="flex items-center gap-3 px-4 py-4 shrink-0"
         style="border-bottom:1px solid rgba(255,255,255,.07); background:#0F1117;">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
             style="background:#E8372C;">
            <i class="fas fa-newspaper text-white text-xs"></i>
        </div>
        <div class="sidebar-logo-text min-w-0">
            <p class="text-white font-display font-semibold text-base leading-tight tracking-tight">Kusoma Newsroom</p>
            <p class="text-[10px] font-mono" style="color:rgba(255,255,255,.28); letter-spacing:.06em;">CMS v4.0</p>
        </div>
    </div>

    {{-- ── Quick publish CTA ── --}}
    <div class="px-3 pt-4 pb-2 shrink-0 sidebar-logo-text">
        <a href="/dashboard/posts/create"
           class="flex items-center justify-center gap-2 w-full py-2 rounded-xl text-white text-xs font-semibold transition-all"
           style="background:linear-gradient(135deg,#E8372C,#C0211A); box-shadow:0 4px 14px rgba(232,55,44,.35);">
            <i class="fas fa-plus text-xs"></i>
            New Article
        </a>
    </div>

    {{-- ── Navigation ── --}}
    <nav class="flex-1 overflow-y-auto px-2 py-2 space-y-0.5" style="scrollbar-width:thin; scrollbar-color:#2A3250 transparent;">
        @foreach ($nav as $group => $items)
            <p class="sidebar-section-label px-3 pt-3 pb-1.5 text-[9.5px] font-semibold uppercase tracking-[.12em]"
               style="color:rgba(255,255,255,.25); {{ $loop->first ? '' : '' }}">
                {{ $group }}
            </p>

            @foreach ($items as $item)
                @php
                    $active = $activeSection === $item['key'];
                    $href   = $item['url'] ?? '#';
                @endphp

                <a href="{{ $href }}"
                   data-section="{{ $item['key'] }}"
                   data-tooltip="{{ $item['label'] }}"
                   class="nav-item {{ $active ? 'active' : '' }} relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium cursor-pointer"
                   style="color: {{ $active ? '#FF6B5B' : 'rgba(255,255,255,.5)' }}">

                    <i class="nav-icon fas {{ $item['icon'] }} text-sm w-4 text-center shrink-0"></i>
                    <span class="nav-label flex-1 truncate">{{ $item['label'] }}</span>

                    @if (!empty($item['badge']))
                        <span class="nav-label ml-auto {{ $item['badge']['cls'] }} {{ !empty($item['badge']['pulse']) ? 'pulse' : '' }} text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">
                            {{ $item['badge']['n'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>

    {{-- ── Editor profile ── --}}
    <div class="shrink-0 px-2 pb-3 pt-2 relative"
         style="border-top:1px solid rgba(255,255,255,.07);">

        {{-- Avatar popup menu --}}
        <div id="avatar-menu"
             class="bg-white rounded-2xl overflow-hidden"
             style="box-shadow:0 -4px 40px rgba(0,0,0,.28); border:1px solid #E1E4EB;">
            <div class="px-4 py-3" style="border-bottom:1px solid #E1E4EB;">
                <p class="text-[13px] font-semibold text-gray-900 truncate">{{ $adminName }}</p>
                <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $adminEmail }}</p>
                <span class="inline-block mt-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-600 uppercase tracking-wide">
                    {{ $adminRole }}
                </span>
            </div>
            <div class="py-1">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-user text-gray-400 text-xs w-4 text-center"></i>
                    My Profile
                </a>
                <a href="/dashboard/settings"
                   class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-sliders text-gray-400 text-xs w-4 text-center"></i>
                    Settings
                </a>
                <a href="/dashboard/team"
                   class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-users-gear text-gray-400 text-xs w-4 text-center"></i>
                    Manage Team
                </a>
            </div>
            <div style="border-top:1px solid #E1E4EB;" class="py-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fas fa-right-from-bracket text-xs w-4 text-center"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        {{-- Profile trigger button --}}
        <button id="avatar-btn"
                class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl transition-colors"
                style="background:rgba(255,255,255,.05);"
                onmouseenter="this.style.background='rgba(255,255,255,.09)'"
                onmouseleave="this.style.background='rgba(255,255,255,.05)'">
            {{-- Avatar circle --}}
            <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center text-white text-sm font-bold"
                 style="background:linear-gradient(135deg,#E8372C,#C0211A);">
                {{ $adminInitial }}
            </div>
            {{-- Name + role --}}
            <div class="nav-label text-left min-w-0 flex-1">
                <p class="text-white text-[12.5px] font-medium truncate leading-tight">{{ $adminName }}</p>
                <p class="text-[10px] leading-tight" style="color:rgba(255,255,255,.35);">{{ $adminRole }}</p>
            </div>
            {{-- Chevron --}}
            <i class="nav-label fas fa-chevron-up text-[9px]" style="color:rgba(255,255,255,.25);"></i>
        </button>
    </div>
</aside>