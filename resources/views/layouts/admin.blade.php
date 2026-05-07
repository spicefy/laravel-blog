@props(['title' => 'Newsroom Admin', 'activeSection' => null])

@php
    // ── Auth guard ──────────────────────────────────────────────
    $user         = auth()->user();
    $isGuest      = is_null($user);

    $adminName    = $user?->name  ?? '';
    $adminEmail   = $user?->email ?? '';
    $adminInitial = $adminName ? strtoupper(mb_substr($adminName, 0, 1)) : '?';
    $adminRole    = 'Editor-in-Chief';

    // ── Active section ──────────────────────────────────────────
    $activeSection = $activeSection ?? request()->segment(2) ?? 'dashboard';

    // ── News-focused navigation ─────────────────────────────────
    $nav = [
        'Overview' => [
            ['key' => 'dashboard',  'icon' => 'fa-gauge-high',   'label' => 'Dashboard',   'url' => '/dashboard'],
            ['key' => 'analytics',  'icon' => 'fa-chart-mixed',  'label' => 'Analytics',   'url' => '/dashboard/analytics'],
        ],
        'Editorial' => [
            ['key' => 'articles',   'icon' => 'fa-newspaper',    'label' => 'Articles',    'url' => '/dashboard/posts',
             'badge' => ['n' => 8, 'cls' => 'bg-amber-500']],
            ['key' => 'breaking',   'icon' => 'fa-bolt',         'label' => 'Breaking News','url' => '/dashboard/breaking',
             'badge' => ['n' => 2, 'cls' => 'bg-red-500', 'pulse' => true]],
            ['key' => 'drafts',     'icon' => 'fa-file-pen',     'label' => 'Drafts',      'url' => '/dashboard/drafts',
             'badge' => ['n' => 5, 'cls' => 'bg-slate-500']],
            ['key' => 'scheduled',  'icon' => 'fa-calendar-clock','label' => 'Scheduled',  'url' => '/dashboard/scheduled'],
            ['key' => 'categories', 'icon' => 'fa-layer-group',  'label' => 'Categories',  'url' => '/dashboard/categories'],
            ['key' => 'tags',       'icon' => 'fa-tags',         'label' => 'Tags',        'url' => '/dashboard/tags'],
        ],
        'Media' => [
            ['key' => 'media',      'icon' => 'fa-photo-film',   'label' => 'Media Library','url' => '/dashboard/media'],
            ['key' => 'videos',     'icon' => 'fa-clapperboard', 'label' => 'Videos',      'url' => '/dashboard/videos'],
        ],
        'Engagement' => [
            ['key' => 'comments',   'icon' => 'fa-comments',     'label' => 'Comments',    'url' => '/dashboard/comments',
             'badge' => ['n' => 14, 'cls' => 'bg-blue-500']],
            ['key' => 'subscribers','icon' => 'fa-envelope-open-text', 'label' => 'Subscribers', 'url' => '/dashboard/subscribers'],
            ['key' => 'reports',    'icon' => 'fa-flag',         'label' => 'Reports',     'url' => '/dashboard/reports',
             'badge' => ['n' => 3, 'cls' => 'bg-red-500', 'pulse' => true]],
        ],
        'System' => [
            ['key' => 'team',       'icon' => 'fa-users-gear',   'label' => 'Team & Roles','url' => '/dashboard/team'],
            ['key' => 'settings',   'icon' => 'fa-sliders',      'label' => 'Settings',    'url' => '/dashboard/settings'],
            ['key' => 'logs',       'icon' => 'fa-terminal',     'label' => 'Audit Logs',  'url' => '/dashboard/logs'],
        ],
    ];

    // ── Notifications ───────────────────────────────────────────
    $notifications = $isGuest ? [] : [
        [
            'icon' => 'fa-bolt', 'iconBg' => 'bg-red-100', 'iconColor' => 'text-red-500',
            'title' => 'Breaking story submitted',
            'body'  => 'Amara Osei filed "Parliament dissolved…"',
            'time'  => '3m ago', 'unread' => true,
        ],
        [
            'icon' => 'fa-comment', 'iconBg' => 'bg-blue-100', 'iconColor' => 'text-blue-500',
            'title' => '14 new comments pending',
            'body'  => 'Awaiting moderation on 6 articles',
            'time'  => '12m ago', 'unread' => true,
        ],
        [
            'icon' => 'fa-calendar-check', 'iconBg' => 'bg-green-100', 'iconColor' => 'text-green-600',
            'title' => 'Article published',
            'body'  => '"Budget review 2025" went live',
            'time'  => '1h ago', 'unread' => false,
        ],
        [
            'icon' => 'fa-flag', 'iconBg' => 'bg-amber-100', 'iconColor' => 'text-amber-500',
            'title' => 'Content flagged',
            'body'  => 'User report on comment #4821',
            'time'  => '2h ago', 'unread' => false,
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title }} — Newsroom CMS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tailwind CDN --}}
    <!-- <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['DM Sans', 'sans-serif'],
                        display: ['Playfair Display', 'Georgia', 'serif'],
                        mono:    ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        ink:     '#0F1117',
                        ink2:    '#1C2033',
                        slate2:  '#2A3250',
                        accent:  '#E8372C',       /* bold editorial red */
                        accentL: '#FF6B5B',
                        gold:    '#D4A847',
                        surface: '#F4F5F7',
                        border:  '#E1E4EB',
                        muted:   '#6B7280',
                        mid:     '#94A3B8',
                    },
                    boxShadow: {
                        'card': '0 1px 3px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.05)',
                        'panel': '0 8px 40px rgba(0,0,0,.14)',
                    },
                },
            },
        };
    </script>
-->
    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ── Sidebar transitions ────────────────────────── */
        #sidebar      { transition: width .28s cubic-bezier(.4,0,.2,1), transform .28s cubic-bezier(.4,0,.2,1); }
        #main-content { transition: margin-left .28s cubic-bezier(.4,0,.2,1); }

        /* ── Sidebar dark skin ──────────────────────────── */
        #sidebar { background: #0F1117; }

        /* ── Nav items ──────────────────────────────────── */
        .nav-item { transition: background .15s, color .15s; }
        .nav-item.active { background: rgba(232,55,44,.15); color: #FF6B5B; }
        .nav-item.active .nav-icon { color: #E8372C; }
        .nav-item:not(.active):hover { background: rgba(255,255,255,.06); color: rgba(255,255,255,.85); }

        /* ── Active left accent bar ─────────────────────── */
        .nav-item.active { position: relative; }
        .nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px; border-radius: 0 3px 3px 0;
            background: #E8372C;
        }

        /* ── Sidebar collapse ───────────────────────────── */
        .sidebar-collapsed .nav-label,
        .sidebar-collapsed .sidebar-logo-text,
        .sidebar-collapsed .sidebar-section-label { display: none !important; }
        .sidebar-collapsed { width: 64px !important; }
        .sidebar-collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }

        /* ── Tooltip (collapsed mode) ───────────────────── */
        #sidebar-tooltip {
            position: fixed; z-index: 9999;
            padding: 4px 10px; background: #1C2033;
            color: #fff; font-size: 12px; font-family: 'DM Sans', sans-serif;
            border-radius: 6px; pointer-events: none;
            opacity: 0; transition: opacity .15s;
            white-space: nowrap; display: none;
            border: 1px solid rgba(255,255,255,.1);
        }
        #sidebar-tooltip::before {
            content: ''; position: absolute;
            right: 100%; top: 50%; transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1C2033;
        }

        /* ── Pulse badge ────────────────────────────────── */
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.3} }
        .pulse { animation: pulse-dot 2s ease-in-out infinite; }

        /* ── Scrollbar ──────────────────────────────────── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #2A3250; border-radius: 8px; }

        /* ── Auth modal ─────────────────────────────────── */
        #auth-modal-backdrop { backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }

        /* ── Dropdown panels ────────────────────────────── */
        .dropdown-panel {
            display: none;
            position: absolute;
            z-index: 999;
            animation: dropIn .18s ease both;
        }
        .dropdown-panel.open { display: block; }
        @keyframes dropIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

        /* ── Avatar popup (upward from sidebar) ─────────── */
        #avatar-menu {
            display: none;
            position: absolute;
            bottom: calc(100% + 8px);
            left: 8px; right: 8px;
            z-index: 200;
            animation: dropIn .18s ease both;
        }
        #avatar-menu.open { display: block; }

        /* ── Page content fade-in ───────────────────────── */
        #page-area { animation: fadeIn .3s ease both; }
        @keyframes fadeIn { from{opacity:0} to{opacity:1} }
    </style>

    {{ $styles ?? '' }}
</head>

<body class="bg-surface font-sans text-gray-900 antialiased flex h-screen overflow-hidden">

    {{-- ══════════════════════════════════════════════════════
         GUEST GATE
         ══════════════════════════════════════════════════════ --}}
    @if ($isGuest ?? false)
        <div id="auth-modal-backdrop"
             class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
             style="background: rgba(15,17,23,0.82)">
            <div class="bg-white rounded-2xl shadow-panel w-full max-w-sm p-8 text-center">
                <div class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-newspaper text-white text-lg"></i>
                </div>
                <h2 class="font-display text-ink text-xl mb-1">Newsroom Access</h2>
                <p class="text-sm text-muted mb-6">Sign in to manage your editorial workspace.</p>
                <a href="{{ route('login') }}"
                   class="block w-full bg-ink hover:bg-ink2 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors mb-3">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </a>
                <a href="{{ route('register') }}"
                   class="block w-full border border-border text-ink text-sm font-semibold py-2.5 rounded-xl hover:bg-surface transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>Request Access
                </a>
                <p class="text-[11px] text-muted mt-5">&copy; {{ date('Y') }} Newsroom CMS</p>
            </div>
        </div>

    @else

    {{-- ══════════════════════════════════════════════════════
         AUTHENTICATED SHELL
         ══════════════════════════════════════════════════════ --}}

    @include('partials.sidebar', [
        'activeSection' => $activeSection,
        'nav'           => $nav,
        'adminName'     => $adminName,
        'adminEmail'    => $adminEmail,
        'adminInitial'  => $adminInitial,
        'adminRole'     => $adminRole,
    ])

    {{-- Mobile overlay --}}
    <div id="mob-overlay"
         class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden backdrop-blur-sm"
         onclick="closeSidebar()">
    </div>

    {{-- Main content area --}}
    <div id="main-content" class="flex-1 flex flex-col overflow-hidden min-w-0">

        @include('partials.topbar', [
            'title'         => $title ?? 'Newsroom',
            'adminName'     => $adminName,
            'adminEmail'    => $adminEmail,
            'adminInitial'  => $adminInitial,
            'notifications' => $notifications,
        ])

        <div id="page-area" class="flex-1 overflow-y-auto flex flex-col">

            @yield('content')

            {{-- Footer --}}
            <footer class="border border-kborder bg-white px-6 lg:px-8 py-4 mt-auto shrink-0 rounded-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2 text-[12px]">
                        <span class="font-display font-semibold text-ink text-sm">Kusoma</span>
                        <span class="text-border">·</span>
                        <span class="text-muted">CMS v4.0</span>
                        <span class="hidden sm:inline text-border">·</span>
                        <span class="hidden sm:inline text-muted">
                            Env: <span class="text-green-600 font-medium">Production</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-[12px] text-muted">
                        <a href="#" class="hover:text-accent transition-colors">Docs</a>
                        <a href="#" class="hover:text-accent transition-colors">Support</a>
                        <a href="#" class="hover:text-accent transition-colors">Changelog</a>
                        <span class="hidden sm:inline text-border">·</span>
                        <span class="hidden sm:inline">&copy; 2017-{{ date('Y') }} Kusoma CMS</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    {{-- Tooltip container --}}
    <div id="sidebar-tooltip"></div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // ── Sidebar ────────────────────────────────────────────
        const sidebar    = document.getElementById('sidebar');
        const mobOverlay = document.getElementById('mob-overlay');

        window.openSidebar = function () {
            sidebar?.classList.remove('-translate-x-full');
            mobOverlay?.classList.remove('hidden');
        };

        window.closeSidebar = function () {
            if (window.innerWidth < 1024) {
                sidebar?.classList.add('-translate-x-full');
                mobOverlay?.classList.add('hidden');
            }
        };

        let collapsed = false;
        window.toggleSidebar = function () {
            if (!sidebar) return;
            collapsed = !collapsed;
            sidebar.classList.toggle('sidebar-collapsed', collapsed);
            const main = document.getElementById('main-content');
            if (main) main.style.marginLeft = '';
        };

        // ── Tooltip (collapsed desktop) ─────────────────────────
        const tooltip = document.getElementById('sidebar-tooltip');
        const isSidebarCollapsed = () => sidebar?.classList.contains('sidebar-collapsed');
        const isDesktop = () => window.innerWidth >= 1024;

        function showTooltip(el, text) {
            if (!tooltip || !el) return;
            const rect = el.getBoundingClientRect();
            tooltip.textContent   = text;
            tooltip.style.display = 'block';
            tooltip.style.left    = (rect.right + 12) + 'px';
            tooltip.style.top     = (rect.top + rect.height / 2 - 12) + 'px';
            requestAnimationFrame(() => { tooltip.style.opacity = '1'; });
        }

        function hideTooltip() {
            if (!tooltip) return;
            tooltip.style.opacity = '0';
            setTimeout(() => {
                if (tooltip.style.opacity === '0') tooltip.style.display = 'none';
            }, 180);
        }

        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('mouseenter', function () {
                if (isDesktop() && isSidebarCollapsed()) {
                    const text = this.getAttribute('data-tooltip');
                    if (text) showTooltip(this, text);
                }
            });
            item.addEventListener('mouseleave', hideTooltip);
        });

        if (sidebar) {
            new MutationObserver(() => {
                if (!isSidebarCollapsed()) hideTooltip();
            }).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        }

        // ── Generic dropdown toggle ─────────────────────────────
        function closeAllDropdowns(except) {
            document.querySelectorAll('.dropdown-panel').forEach(p => {
                if (p !== except) p.classList.remove('open');
            });
        }

        document.querySelectorAll('[data-dropdown]').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const target = document.getElementById(this.dataset.dropdown);
                if (!target) return;
                const opening = !target.classList.contains('open');
                closeAllDropdowns(opening ? target : null);
                target.classList.toggle('open', opening);
            });
        });

        document.addEventListener('click', () => closeAllDropdowns());

        // ── Avatar menu (sidebar) ───────────────────────────────
        const avatarBtn  = document.getElementById('avatar-btn');
        const avatarMenu = document.getElementById('avatar-menu');

        if (avatarBtn && avatarMenu) {
            avatarBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                avatarMenu.classList.toggle('open');
            });
            document.addEventListener('click', (e) => {
                if (!avatarBtn.contains(e.target) && !avatarMenu.contains(e.target)) {
                    avatarMenu.classList.remove('open');
                }
            });
        }
    });
    </script>

    {{ $scripts ?? '' }}

    @endif

</body>
</html>