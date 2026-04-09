@props(['title' => 'Dashboard', 'activeSection' => null])

@php
    // ── Auth guard ──────────────────────────────────────────────────────────────
    $user         = auth()->user();
    $isGuest      = is_null($user);

    $adminName    = $user?->name    ?? '';
    $adminEmail   = $user?->email   ?? '';
    $adminInitial = $adminName ? strtoupper(mb_substr($adminName, 0, 1)) : '?';
    
    // ── Simple role assignment (no Spatie package needed) ──────────────────────
    $adminRole = 'Admin'; // Default role
    
    // ── Active section ──────────────────────────────────────────────────────────
    $activeSection = $activeSection ?? request()->segment(2) ?? 'index';

    // ── Navigation (same as before) ────────────────────────────────────────────
    $nav = [
        'Overview' => [
            ['key' => 'index',     'icon' => 'fa-table-cells', 'label' => 'Dashboard', 'url' => '/dashboard'],
            ['key' => 'analytics', 'icon' => 'fa-chart-line',  'label' => 'Analytics', 'url' => '/analytics'],
        ],
        'Content' => [
            ['key' => 'posts',          'icon' => 'fa-newspaper', 'label' => 'Posts',          'url' => null,                       'badge' => ['n' => 14, 'cls' => 'bg-red-500']],
            ['key' => 'exams',          'icon' => 'fa-folder',    'label' => 'Exams',          'url' => '/dashboard/exams'],
            ['key' => 'grade-subjects', 'icon' => 'fa-book',      'label' => 'Grade Subjects', 'url' => '/dashboard/grade-subject'],
            ['key' => 'comments',       'icon' => 'fa-comments',  'label' => 'Comments',       'url' => '/dashboard/grade-subject', 'badge' => ['n' => 6, 'cls' => 'bg-korange']],
            ['key' => 'tags',           'icon' => 'fa-tags',      'label' => 'Tags',           'url' => null],
        ],
        'Community' => [
            ['key' => 'Users',   'icon' => 'fa-users', 'label' => 'Users & Members', 'url' => '/dashboard/users'],
            ['key' => 'reports', 'icon' => 'fa-flag',  'label' => 'Reports',         'url' => null, 'badge' => ['n' => 9, 'cls' => 'bg-red-500', 'pulse' => true]],
            ['key' => 'bans',    'icon' => 'fa-ban',   'label' => 'Bans',            'url' => null],
        ],
        'System' => [
            ['key' => 'settings', 'icon' => 'fa-gear',     'label' => 'Settings',    'url' => null],
            ['key' => 'logs',     'icon' => 'fa-terminal', 'label' => 'System Logs', 'url' => null],
        ],
    ];

    // ── Notifications (same as before) ──────────────────────────────────────────
    $notifications = $isGuest ? [] : [
        [
            'icon'      => 'fa-user-plus',
            'iconBg'    => 'bg-blue-100',
            'iconColor' => 'text-blue-500',
            'title'     => 'New member joined',
            'body'      => 'ZaraHealthNG just registered',
            'time'      => '2m ago',
            'unread'    => true,
        ],
        [
            'icon'      => 'fa-flag',
            'iconBg'    => 'bg-green-100',
            'iconColor' => 'text-red-400',
            'title'     => 'New report filed',
            'body'      => 'TrollBuster99 was reported',
            'time'      => '15m ago',
            'unread'    => true,
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
{{-- resources/views/layouts/admin.blade.php --}}
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Kusoma Dashboard – {{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- 1. Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- 2. Tailwind config (must come AFTER CDN script) --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['DM Sans', 'sans-serif'], display: ['Lora', 'serif'] },
                    colors: {
                        navy:    '#1a2c5b',
                        royal:   '#2755c8',
                        mid:     '#4a72e8',
                        klight:  '#eef2fb',
                        kgreen:  '#1a7a45',
                        korange: '#d97706',
                        kbg:     '#f7f8fa',
                        kborder: '#e2e6ed',
                        muted:   '#6b7280',
                    },
                },
            },
        };
    </script>

    {{-- 3. Fonts & icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        /* ── Layout transitions ───────────────────────── */
        #sidebar      { transition: transform .3s cubic-bezier(.4,0,.2,1), width .3s cubic-bezier(.4,0,.2,1); }
        #main-content { transition: margin-left .3s cubic-bezier(.4,0,.2,1); }

        /* ── Nav items ────────────────────────────────── */
        .nav-item.active                  { background: rgba(255,255,255,.12); color: #fff; }
        .nav-item.active .nav-icon        { color: #7da8f5; }
        .nav-item:not(.active):hover      { background: rgba(255,255,255,.07); }
        .tr-hover:hover                   { background: #f7f8fa; }

        /* ── Pulse animation ──────────────────────────── */
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }
        .pulse { animation: pulse-dot 2s infinite; }

        /* ── Scrollbar ────────────────────────────────── */
        ::-webkit-scrollbar       { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d9e6; border-radius:8px; }

        /* ── Dropdown styles ──────────────────────────── */
        .dropdown-menu {
            display: none;
        }
        .dropdown-menu.open,
        .dropdown-menu.show {
            display: block;
        }

        /* ── Sidebar avatar menu ──────────────────────── */
        #avatar-menu {
            display: none;
            position: absolute;
            bottom: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 50;
        }
        #avatar-menu.open,
        #avatar-menu.show {
            display: block;
        }

        /* ── Collapsed sidebar ────────────────────────── */
        .sidebar-collapsed .nav-label,
        .sidebar-collapsed .sidebar-logo-text,
        .sidebar-collapsed .sidebar-section-label { display: none; }
        .sidebar-collapsed { width: 68px !important; }

        /* ── Collapsed tooltip ────────────────────────── */
        #sidebar-tooltip {
            position: fixed;
            z-index: 50;
            padding: 2px 8px;
            background: #111827;
            color: #fff;
            font-size: 12px;
            border-radius: 4px;
            pointer-events: none;
            opacity: 0;
            transition: opacity .15s;
            display: none;
        }
        #sidebar-tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border-width: 4px 4px 4px 0;
            border-style: solid;
            border-color: transparent #111827 transparent transparent;
        }

        /* ── Auth modal ───────────────────────────────── */
        #auth-modal-backdrop {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        /* ── Utility classes ──────────────────────────── */
        .hidden {
            display: none;
        }
    </style>

    {{ $styles ?? '' }}
</head>

<body class="bg-kbg font-sans text-gray-900 antialiased flex h-screen overflow-hidden">

    {{-- ════════════════════════════════════════════════════════════
         UNAUTHENTICATED: show a blocking modal, hide shell content
         ════════════════════════════════════════════════════════════ --}}
    @if ($isGuest ?? false)
        <div id="auth-modal-backdrop"
             class="fixed inset-0 z-[9999] bg-navy/70 flex items-center justify-center px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center">

                {{-- Logo mark --}}
                <div class="w-12 h-12 bg-royal rounded-xl flex items-center justify-center mx-auto mb-4">
                    <span class="font-display font-semibold text-white text-xl">K</span>
                </div>

                <h2 class="font-display font-semibold text-navy text-xl mb-1">Access Restricted</h2>
                <p class="text-sm text-muted mb-6">
                    You need to be signed in to view the Kusoma dashboard.
                </p>

                <a href="{{ route('login') }}"
                   class="block w-full bg-royal hover:bg-mid text-white text-sm font-semibold py-2.5 rounded-lg transition-colors mb-3">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </a>

                <a href="{{ route('register') }}"
                   class="block w-full bg-kbg hover:bg-kborder text-navy text-sm font-semibold py-2.5 rounded-lg border border-kborder transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>Create an Account
                </a>

                <p class="text-[11px] text-muted mt-5">
                    &copy; {{ date('Y') }} Kusoma · Admin Panel
                </p>
            </div>
        </div>

        {{-- Render nothing else for guests --}}
    @else

    {{-- ════════════════════════════════════════════════════════════
         AUTHENTICATED: full dashboard shell
         ════════════════════════════════════════════════════════════ --}}

    @include('partials.sidebar', [
        'activeSection' => $activeSection ?? 'index',
        'nav'           => $nav ?? [],
        'adminName'     => $adminName ?? '',
        'adminEmail'    => $adminEmail ?? '',
        'adminInitial'  => $adminInitial ?? '?',
        'adminRole'     => $adminRole ?? 'Admin',
    ])

    <div id="mob-overlay"
         class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"
         onclick="closeSidebar()">
    </div>

    <div id="main-content" class="flex-1 flex flex-col overflow-hidden min-w-0">

        @include('partials.topbar', [
            'title'         => $title ?? 'Dashboard',
            'adminName'     => $adminName ?? '',
            'adminEmail'    => $adminEmail ?? '',
            'adminInitial'  => $adminInitial ?? '?',
            'notifications' => $notifications ?? [],
        ])

        <div id="page-area" class="flex-1 overflow-y-auto flex flex-col">

            @yield('content')

            <footer class="border-t border-kborder bg-white px-5 lg:px-7 py-4 mt-auto shrink-0">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-display font-semibold text-navy text-[15px]">kusoma</span>
                        <span class="text-kborder">·</span>
                        <span class="text-[12px] text-muted">Admin Panel v3.4.1</span>
                        <span class="hidden sm:inline text-kborder">·</span>
                        <span class="hidden sm:inline text-[12px] text-muted">
                            Environment: <span class="text-kgreen font-medium">Production</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-[12px] text-muted">
                        <a href="#" class="hover:text-royal transition-colors">Documentation</a>
                        <a href="#" class="hover:text-royal transition-colors">Support</a>
                        <a href="#" class="hover:text-royal transition-colors">Changelog</a>
                        <span class="text-kborder hidden sm:inline">·</span>
                        <span class="hidden sm:inline">&copy; {{ date('Y') }} Kusoma</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <div id="sidebar-tooltip"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // ── Sidebar ──────────────────────────────────────────────
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
            };

            // ── Sidebar Tooltips (collapsed desktop only) ────────────
            const tooltip = document.getElementById('sidebar-tooltip');

            const isSidebarCollapsed = () => sidebar?.classList.contains('sidebar-collapsed');
            const isDesktop = () => window.innerWidth >= 1024;

            function showTooltip(el, text) {
                if (!tooltip || !el) return;
                const rect = el.getBoundingClientRect();
                tooltip.textContent   = text;
                tooltip.style.display = 'block';
                tooltip.style.left    = (rect.right + 10) + 'px';
                tooltip.style.top     = (rect.top + rect.height / 2 - 10) + 'px';
                setTimeout(() => { tooltip.style.opacity = '1'; }, 10);
            }

            function hideTooltip() {
                if (!tooltip) return;
                tooltip.style.opacity = '0';
                setTimeout(() => {
                    if (tooltip.style.opacity === '0') tooltip.style.display = 'none';
                }, 200);
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

            window.addEventListener('resize', () => {
                if (!isSidebarCollapsed() || !isDesktop()) hideTooltip();
            });

            // ── DROPDOWN TOGGLES ──────────────────────────────────────
            // Toggle dropdowns when clicking on trigger buttons
            const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
            
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const dropdownId = this.getAttribute('data-dropdown-toggle');
                    const dropdown = document.getElementById(dropdownId);
                    
                    if (dropdown) {
                        // Close all other dropdowns first
                        document.querySelectorAll('.dropdown-menu, [id$="-menu"]').forEach(menu => {
                            if (menu.id !== dropdownId) {
                                menu.classList.add('hidden');
                                menu.classList.remove('open', 'show');
                            }
                        });
                        
                        // Toggle the current dropdown
                        dropdown.classList.toggle('hidden');
                        dropdown.classList.toggle('open');
                        dropdown.classList.toggle('show');
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                // Check if click is outside any dropdown toggle and dropdown menu
                const isInsideToggle = e.target.closest('[data-dropdown-toggle]');
                const isInsideDropdown = e.target.closest('.dropdown-menu, [id$="-menu"]');
                
                if (!isInsideToggle && !isInsideDropdown) {
                    document.querySelectorAll('.dropdown-menu, [id$="-menu"]').forEach(menu => {
                        menu.classList.add('hidden');
                        menu.classList.remove('open', 'show');
                    });
                }
            });
            
            // ── Avatar Menu Dropdown ─────────────────────────────────
            const avatarBtn = document.getElementById('avatar-btn');
            const avatarMenu = document.getElementById('avatar-menu');
            
            if (avatarBtn && avatarMenu) {
                avatarBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    avatarMenu.classList.toggle('hidden');
                    avatarMenu.classList.toggle('open');
                    avatarMenu.classList.toggle('show');
                });
            }
            
            // ── Notification Dropdown ────────────────────────────────
            const notificationBtn = document.getElementById('notification-btn');
            const notificationMenu = document.getElementById('notification-menu');
            
            if (notificationBtn && notificationMenu) {
                notificationBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    notificationMenu.classList.toggle('hidden');
                    notificationMenu.classList.toggle('open');
                    notificationMenu.classList.toggle('show');
                });
            }
            
            // ── User Dropdown (alternative) ──────────────────────────
            const userMenuBtn = document.getElementById('user-menu-button');
            const userDropdown = document.getElementById('user-dropdown');
            
            if (userMenuBtn && userDropdown) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    userDropdown.classList.toggle('hidden');
                    userDropdown.classList.toggle('open');
                    userDropdown.classList.toggle('show');
                });
            }
        });
    </script>

    {{ $scripts ?? '' }}

    @endif {{-- end @if ($isGuest) --}}

</body>
</html>