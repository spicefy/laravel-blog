{{--
    partials/sidebar.blade.php

    All variables are injected by layouts/dashboard.blade.php via:
        @include('partials.sidebar', [...])

    Expected vars:
        $activeSection  string   – nav key of the current page
        $nav            array    – grouped nav items (built in layout)
        $adminName      string
        $adminEmail     string
        $adminInitial   string
        $adminRole      string
--}}

<aside id="sidebar"
    class="fixed lg:relative inset-y-0 left-0 z-40 w-60 bg-navy flex flex-col -translate-x-full lg:translate-x-0">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10 shrink-0">
        <div class="w-8 h-8 bg-royal rounded-lg flex items-center justify-center shrink-0">
            <span class="font-display font-semibold text-white text-sm">K</span>
        </div>
        <span class="sidebar-logo-text font-display font-semibold text-white text-xl tracking-tight">Kusoma</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
        @foreach ($nav as $group => $items)
            <p class="sidebar-section-label text-[10px] font-semibold uppercase tracking-widest text-white/30 px-3 pb-1.5 {{ $loop->first ? 'pt-1' : 'pt-3' }}">
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
                    class="nav-item {{ $active ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/{{ $active ? '80' : '60' }} text-[13.5px] font-medium transition-colors cursor-pointer">

                    <i class="nav-icon fas {{ $item['icon'] }} text-sm w-5 text-center"></i>
                    <span class="nav-label">{{ $item['label'] }}</span>

                    @if (!empty($item['badge']))
                        <span class="nav-label ml-auto {{ $item['badge']['cls'] }} {{ !empty($item['badge']['pulse']) ? 'pulse' : '' }} text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                            {{ $item['badge']['n'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>

    {{-- Profile area with Alpine.js dropdown --}}
    <div x-data="{ open: false }" class="border-t border-white/10 px-3 py-3 shrink-0 relative">

        {{-- Trigger button --}}
        <button @click="open = !open"
                @click.away="open = false"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-white/10 transition-colors">
            <div class="w-8 h-8 rounded-full bg-blue-800 flex items-center justify-center text-white text-sm font-bold shrink-0">
                {{ $adminInitial }}
            </div>
            <div class="nav-label text-left min-w-0 flex-1">
                <p class="text-white text-[13px] font-medium truncate leading-tight">{{ $adminName }}</p>
                <p class="text-white/40 text-[11px] leading-tight">{{ $adminRole }}</p>
            </div>
            <i :class="open ? 'rotate-180' : ''" class="nav-label fas fa-chevron-up text-white/30 text-[10px] transition-transform duration-200"></i>
        </button>

        {{-- Popup menu (opens above button) --}}
        <div x-show="open"
             x-transition
             class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-xl border border-kborder shadow-lg overflow-hidden w-full z-50">
            <div class="px-4 py-3 border-b border-kborder">
                <p class="text-[13px] font-semibold text-navy">{{ $adminName }}</p>
                <p class="text-[11px] text-muted">{{ $adminEmail }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-700 hover:bg-kbg transition-colors">
                <i class="fas fa-user text-muted text-xs w-4 text-center"></i> View Profile
            </a>
            <a href="/settings" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-gray-700 hover:bg-kbg transition-colors">
                <i class="fas fa-gear text-muted text-xs w-4 text-center"></i> Settings
            </a>
            <hr class="border-kborder" />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] text-red-600 hover:bg-red-50 transition-colors">
                    <i class="fas fa-right-from-bracket text-xs w-4 text-center"></i> Log out
                </button>
            </form>
        </div>
    </div>
</aside>