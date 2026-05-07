{{--
    partials/topbar.blade.php — Newsroom CMS
    ─────────────────────────────────────────
    Expected vars:
        $title          string
        $adminName      string
        $adminEmail     string
        $adminInitial   string
        $notifications  array
--}}

@php
    $unreadCount = collect($notifications)->where('unread', true)->count();
@endphp

<header class="shrink-0 h-14 flex items-center px-4 gap-3 z-20 bg-white"
        style="border-bottom:1px solid #E1E4EB;">

    {{-- ── Mobile menu open ── --}}
    <button class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg transition-colors text-gray-500 hover:bg-gray-100"
            onclick="openSidebar()" aria-label="Open menu">
        <i class="fas fa-bars text-sm"></i>
    </button>

    {{-- ── Desktop sidebar collapse ── --}}
    <button class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg transition-colors text-gray-400 hover:text-gray-800 hover:bg-gray-100"
            onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <i class="fas fa-bars-staggered text-sm"></i>
    </button>

    {{-- ── Breadcrumb / page title ── --}}
    <div class="flex items-center gap-2 min-w-0">
        <span class="text-gray-400 text-sm hidden sm:inline">
            <i class="fas fa-newspaper text-xs" style="color:#E8372C;"></i>
        </span>
        <span class="text-gray-300 hidden sm:inline text-sm">/</span>
        <h1 id="page-title" class="font-display font-semibold text-[16px] text-gray-900 truncate">
            {{ $title }}
        </h1>
    </div>

    {{-- ── Spacer ── --}}
    <div class="flex-1"></div>

    {{-- ── Breaking news quick-post ── --}}
    <a href="/dashboard/breaking/create"
       class="hidden md:flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all"
       style="background:#E8372C; color:#fff; box-shadow:0 2px 8px rgba(232,55,44,.3);"
       onmouseenter="this.style.background='#C0211A'"
       onmouseleave="this.style.background='#E8372C'">
        <i class="fas fa-bolt text-[11px]"></i>
        Breaking
    </a>

    {{-- ── Global search ── --}}
    <div class="relative hidden sm:block">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
        <input type="search"
               placeholder="Search articles, authors…"
               class="text-sm pl-8 pr-3 py-1.5 rounded-lg outline-none transition-all w-44 focus:w-56"
               style="background:#F4F5F7; border:1px solid #E1E4EB; color:#0F1117; font-family:'DM Sans',sans-serif;"
               onfocus="this.style.borderColor='#E8372C'; this.style.background='#fff';"
               onblur="this.style.borderColor='#E1E4EB'; this.style.background='#F4F5F7';" />
    </div>

    {{-- ── Notification bell ── --}}
    <div class="relative">
        <button id="notif-btn"
                data-dropdown="notif-panel"
                class="relative w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors"
                aria-label="Notifications">
            <i class="fas fa-bell text-[15px]"></i>
            @if ($unreadCount > 0)
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full pulse
                             ring-2 ring-white"></span>
            @endif
        </button>

        {{-- Notification panel --}}
        <div id="notif-panel"
             class="dropdown-panel right-0 mt-2 w-80 bg-white rounded-2xl overflow-hidden"
             style="box-shadow:0 8px 40px rgba(0,0,0,.14); border:1px solid #E1E4EB; top:100%;">

            {{-- Panel header --}}
            <div class="flex items-center justify-between px-4 py-3"
                 style="border-bottom:1px solid #E1E4EB;">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-900">Notifications</span>
                    @if ($unreadCount > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-red-100 text-red-600">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </div>
                <button class="text-xs font-medium transition-colors" style="color:#E8372C;"
                        onmouseenter="this.style.opacity='.7'" onmouseleave="this.style.opacity='1'">
                    Mark all read
                </button>
            </div>

            {{-- Items --}}
            <ul class="divide-y max-h-72 overflow-y-auto" style="border-color:#F0F2F5;">
                @forelse ($notifications as $notif)
                    <li class="flex items-start gap-3 px-4 py-3 cursor-pointer transition-colors"
                        style="background:#fff;"
                        onmouseenter="this.style.background='#F9FAFB'"
                        onmouseleave="this.style.background='#fff'">
                        <div class="w-8 h-8 rounded-full {{ $notif['iconBg'] }} flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas {{ $notif['icon'] }} {{ $notif['iconColor'] }} text-[11px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[12.5px] font-semibold text-gray-800 leading-tight">{{ $notif['title'] }}</p>
                            <p class="text-[11.5px] text-gray-500 mt-0.5 leading-tight truncate">{{ $notif['body'] }}</p>
                            <p class="text-[10.5px] text-gray-400 mt-1">{{ $notif['time'] }}</p>
                        </div>
                        @if ($notif['unread'])
                            <span class="w-2 h-2 rounded-full shrink-0 mt-2" style="background:#E8372C;"></span>
                        @endif
                    </li>
                @empty
                    <li class="px-4 py-8 text-center">
                        <i class="fas fa-bell-slash text-gray-300 text-2xl mb-2 block"></i>
                        <p class="text-xs text-gray-400">All caught up!</p>
                    </li>
                @endforelse
            </ul>

            {{-- Panel footer --}}
            <div class="px-4 py-2.5 text-center" style="border-top:1px solid #F0F2F5;">
                <a href="/dashboard/notifications" class="text-xs font-medium transition-colors" style="color:#E8372C;">
                    View all notifications →
                </a>
            </div>
        </div>
    </div>

    {{-- ── User dropdown ── --}}
    <div class="relative">
        <button id="user-btn"
                data-dropdown="user-panel"
                class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-full hover:bg-gray-100 transition-colors border border-transparent hover:border-gray-200"
                style="background:#fff;">
            {{-- Avatar --}}
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                 style="background:linear-gradient(135deg,#E8372C,#C0211A);">
                {{ $adminInitial }}
            </div>
            <span class="text-xs font-medium text-gray-800 hidden xl:inline max-w-[100px] truncate">
                {{ $adminName }}
            </span>
            <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
        </button>

        {{-- User panel --}}
        <div id="user-panel"
             class="dropdown-panel right-0 mt-2 w-52 bg-white rounded-2xl overflow-hidden"
             style="box-shadow:0 8px 40px rgba(0,0,0,.14); border:1px solid #E1E4EB; top:100%;">

            {{-- Identity --}}
            <div class="px-4 py-3" style="border-bottom:1px solid #E1E4EB; background:#F9FAFB;">
                <p class="text-[12.5px] font-semibold text-gray-900 truncate">{{ $adminName }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5 truncate">{{ $adminEmail }}</p>
            </div>

            {{-- Menu items --}}
            <ul class="py-1">
                <li>
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-4 py-2.5 text-[12.5px] text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-user-circle text-gray-400 text-xs w-4 text-center"></i>
                        Edit Profile
                    </a>
                </li>
                <li>
                    <a href="/dashboard/settings"
                       class="flex items-center gap-3 px-4 py-2.5 text-[12.5px] text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-sliders text-gray-400 text-xs w-4 text-center"></i>
                        Settings
                    </a>
                </li>
                <li>
                    <a href="/dashboard/team"
                       class="flex items-center gap-3 px-4 py-2.5 text-[12.5px] text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-users-gear text-gray-400 text-xs w-4 text-center"></i>
                        Manage Team
                    </a>
                </li>
                <li>
                    <a href="/dashboard/logs"
                       class="flex items-center gap-3 px-4 py-2.5 text-[12.5px] text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-shield-halved text-gray-400 text-xs w-4 text-center"></i>
                        Audit Logs
                    </a>
                </li>
            </ul>

            {{-- Sign out --}}
            <div class="py-1" style="border-top:1px solid #F0F2F5;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-[12.5px] text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fas fa-right-from-bracket text-xs w-4 text-center"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>

</header>