{{--
    views\partials\topbar.blade.php
--}}

@php
    $unreadCount = collect($notifications)->where('unread', true)->count();
@endphp

<header class="bg-white border-b border-kborder h-14 flex items-center px-5 gap-4 shrink-0 z-20">

    {{-- Desktop sidebar collapse --}}
    <button id="desktop-menu-btn"
        class="hidden lg:flex text-muted text-xl hover:text-royal transition-colors"
        onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Mobile sidebar open --}}
    <button class="lg:hidden text-muted text-xl" onclick="openSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div id="page-title" class="font-display font-semibold text-[17px] text-navy">
        {{ $title }}
    </div>

    <div class="flex items-center gap-3 ml-auto">

        {{-- Search --}}
        <div class="relative hidden sm:block">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-muted text-xs pointer-events-none"></i>
            <input type="text" placeholder="Search..."
                class="border border-kborder bg-kbg rounded-lg pl-8 pr-3 py-1.5 text-sm w-44 outline-none focus:border-royal focus:bg-white transition-all font-sans" />
        </div>

        {{-- Notification bell (using Alpine.js) --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    @click.away="open = false"
                    class="relative w-9 h-9 flex items-center justify-center text-muted hover:text-gray-800 rounded-lg hover:bg-kbg transition-colors">
                <i class="fas fa-bell text-base"></i>
                @if ($unreadCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full pulse"></span>
                @endif
            </button>

            <div x-show="open" 
                 x-transition
                 class="absolute right-0 mt-2 w-80 bg-white border border-kborder rounded-xl shadow-lg z-[999]">
                <div class="flex items-center justify-between px-4 py-3 border-b border-kborder">
                    <span class="text-sm font-semibold text-navy">Notifications</span>
                    <span class="text-xs text-mid font-medium cursor-pointer hover:text-royal">Mark all read</span>
                </div>
                <ul class="divide-y divide-kborder max-h-72 overflow-y-auto">
                    @forelse ($notifications as $notif)
                        <li class="flex items-start gap-3 px-4 py-3 hover:bg-kbg cursor-pointer transition-colors">
                            <div class="w-8 h-8 rounded-full {{ $notif['iconBg'] }} flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fas {{ $notif['icon'] }} {{ $notif['iconColor'] }} text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-navy">{{ $notif['title'] }}</p>
                                <p class="text-xs text-muted mt-0.5">{{ $notif['body'] }}</p>
                                <p class="text-[11px] text-muted mt-1">{{ $notif['time'] }}</p>
                            </div>
                            @if ($notif['unread'])
                                <span class="w-2 h-2 bg-red-500 rounded-full shrink-0 mt-2"></span>
                            @endif
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-xs text-muted">
                            <i class="fas fa-bell-slash text-muted mb-2 block"></i>
                            No notifications yet
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Profile dropdown (using Alpine.js) --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    @click.away="open = false"
                    class="flex items-center gap-2 bg-white border border-kborder rounded-full pl-1 pr-3 py-1 hover:bg-kbg transition-colors shadow-sm">
                <div class="w-7 h-7 rounded-full bg-blue-800 flex items-center justify-center text-white text-xs font-bold">
                    {{ $adminInitial }}
                </div>
                <span class="text-xs font-medium text-navy hidden xl:inline">{{ $adminName }}</span>
                <i :class="open ? 'rotate-180' : ''" class="fas fa-chevron-down text-[10px] text-muted transition-transform duration-200"></i>
            </button>

            <div x-show="open" 
                 x-transition
                 class="absolute right-0 mt-2 w-52 bg-white border border-kborder rounded-xl shadow-lg z-[999]">
                <div class="px-4 py-3 border-b border-kborder">
                    <p class="text-xs font-semibold text-navy">{{ $adminName }}</p>
                    <p class="text-[11px] text-muted mt-0.5">{{ $adminEmail }}</p>
                </div>
                <ul class="py-1">
                    <li>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-xs text-gray-700 hover:bg-kbg transition-colors">
                            <i class="fas fa-user-circle text-muted text-sm w-4 text-center"></i> Edit Profile
                        </a>
                    </li>
                    <li>
                        <a href="/settings" class="flex items-center gap-3 px-4 py-2 text-xs text-gray-700 hover:bg-kbg transition-colors">
                            <i class="fas fa-cog text-muted text-sm w-4 text-center"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a href="/security" class="flex items-center gap-3 px-4 py-2 text-xs text-gray-700 hover:bg-kbg transition-colors">
                            <i class="fas fa-shield-alt text-muted text-sm w-4 text-center"></i> Security
                        </a>
                    </li>
                </ul>
                <div class="border-t border-kborder py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2 text-xs text-red-500 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt text-sm w-4 text-center"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>