{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  {{-- ── Primary SEO ──────────────────────────────────────────────────── --}}
  <title>@yield('title', config('app.name')) – Kusoma</title>
  <meta name="description"  content="@yield('meta_description', 'Breaking education and tech news across Africa.')">
  <meta name="keywords"     content="@yield('meta_keywords', 'education news, Kenya, Africa, KCSE, university')">
  <meta name="robots"       content="@yield('robots', 'index, follow')">
  <link rel="canonical"     href="@yield('canonical', url()->current())">

  {{-- ── Open Graph ──────────────────────────────────────────────────── --}}
  <meta property="og:type"        content="@yield('og_type', 'website')">
  <meta property="og:site_name"   content="Kusoma">
  <meta property="og:title"       content="@yield('og_title', config('app.name'))">
  <meta property="og:description" content="@yield('og_description', 'Breaking education and tech news across Africa.')">
  <meta property="og:image"       content="@yield('og_image', asset('images/og-default.png'))">
  <meta property="og:url"         content="@yield('canonical', url()->current())">
  <meta property="og:locale"      content="en_KE">

  {{-- ── Twitter Card ─────────────────────────────────────────────────── --}}
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:site"        content="@yield('twitter_site', '@KusomaNews')">
  <meta name="twitter:title"       content="@yield('og_title', config('app.name'))">
  <meta name="twitter:description" content="@yield('og_description', '')">
  <meta name="twitter:image"       content="@yield('og_image', asset('images/og-default.png'))">

  {{-- ── Structured Data (JSON-LD) injected per page ─────────────────── --}}
  @yield('structured_data')

  {{-- ── Feeds & extras ─────────────────────────────────────────────── --}}
  <link rel="alternate" type="application/rss+xml" title="Kusoma RSS" href="{{ route('feed.rss') }}">
<script src="https://cdn.tailwindcss.com"></script>
  {{-- ── Fonts & CSS ─────────────────────────────────────────────────── --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])

   <!-- Tailwind Custom Configuration -->
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
<body class="bg-kbg font-body antialiased text-gray-800">

  {{-- ── Navigation ─────────────────────────────────────────────────── --}}
  <x-ui.marketing.nav />

  {{-- ── Main content ────────────────────────────────────────────────── --}}
  @yield('content')

  {{-- ── Footer ──────────────────────────────────────────────────────── --}}
  <x-ui.marketing.footer />

</body>
</html>
