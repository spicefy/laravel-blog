<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            :root {
                --accent:        #6C63FF;
                --accent-hover:  #5A52E0;
                --accent-glow:   rgba(108, 99, 255, 0.45);
                --accent-light:  #A89EFF;
                --bg-deep:       #0A0918;
                --glass:         rgba(255,255,255,0.07);
                --glass-border:  rgba(255,255,255,0.13);
                --text:          #EEEDf8;
                --text-muted:    rgba(238,237,248,0.50);
                --input-bg:      rgba(255,255,255,0.07);
                --input-border:  rgba(255,255,255,0.16);
                --error:         #FF6B7A;
            }

            html, body { height: 100%; }

            body {
                font-family: 'DM Sans', sans-serif;
                font-size: 15px;
                background: var(--bg-deep);
                color: var(--text);
                -webkit-font-smoothing: antialiased;
                overflow-x: hidden;
            }

            /* ─── SCENE ─── */
            .scene {
                position: fixed;
                inset: 0;
                z-index: 0;
                pointer-events: none;
            }

            .scene-img {
                position: absolute;
                inset: 0;
                background:
                    url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&q=75&auto=format&fit=crop') center/cover no-repeat;
                opacity: 0.14;
                filter: saturate(1.6) hue-rotate(200deg);
            }

            .scene-overlay {
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(ellipse 90% 60% at 15% 5%,  rgba(108,99,255,.40) 0%, transparent 55%),
                    radial-gradient(ellipse 70% 50% at 85% 95%, rgba(168,85,247,.30) 0%, transparent 55%),
                    linear-gradient(170deg, #0A0918 0%, #100E2B 60%, #0B1020 100%);
            }

            .orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                animation: drift 14s ease-in-out infinite;
            }
            .orb-a { width:520px;height:520px; background:radial-gradient(circle,#6C63FF,transparent 70%); top:-160px; left:-140px; opacity:.30; animation-delay:0s; }
            .orb-b { width:420px;height:420px; background:radial-gradient(circle,#A855F7,transparent 70%); bottom:-80px; right:-80px; opacity:.28; animation-delay:-5s; }
            .orb-c { width:280px;height:280px; background:radial-gradient(circle,#3B82F6,transparent 70%); top:55%; left:62%; opacity:.22; animation-delay:-9s; }

            @keyframes drift {
                0%,100% { transform: translate(0,0) scale(1); }
                33%      { transform: translate(35px,-28px) scale(1.06); }
                66%      { transform: translate(-22px,18px) scale(0.94); }
            }

            .scene-grid {
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(108,99,255,.055) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(108,99,255,.055) 1px, transparent 1px);
                background-size: 64px 64px;
                mask-image: radial-gradient(ellipse 75% 75% at 50% 50%, black 20%, transparent 100%);
            }

            /* ─── LAYOUT ─── */
            .page {
                position: relative;
                z-index: 1;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
            }

            .card-wrap {
                width: 100%;
                max-width: 468px;
                animation: fadeUp .65s cubic-bezier(.16,1,.3,1) both;
            }

            @keyframes fadeUp {
                from { opacity:0; transform:translateY(28px); }
                to   { opacity:1; transform:translateY(0); }
            }

            /* ─── LOGO ─── */
            .logo-area {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 1.75rem;
                text-decoration: none;
            }

            .logo-badge {
                width: 58px; height: 58px;
                border-radius: 16px;
                background: linear-gradient(135deg, #6C63FF 0%, #A855F7 100%);
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 0 0 1px rgba(108,99,255,.4), 0 10px 36px rgba(108,99,255,.45);
                transition: transform .3s ease, box-shadow .3s ease;
            }
            .logo-badge:hover { transform:scale(1.1) rotate(-4deg); box-shadow: 0 0 0 1px rgba(108,99,255,.6), 0 14px 48px rgba(108,99,255,.6); }
            .logo-badge svg { width:30px; height:30px; fill:white; color:white; }

            .brand { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; letter-spacing:.05em; color:var(--text); margin-top:.65rem; text-transform:uppercase; }

            /* ─── AUTH CARD ─── */
            .auth-card {
                background: rgba(255,255,255,0.065);
                backdrop-filter: blur(32px) saturate(1.6);
                -webkit-backdrop-filter: blur(32px) saturate(1.6);
                border: 1px solid var(--glass-border);
                border-radius: 24px;
                overflow: hidden;
                box-shadow:
                    0 0 0 1px rgba(108,99,255,.10),
                    0 28px 88px rgba(0,0,0,.60),
                    inset 0 1px 0 rgba(255,255,255,.11);
            }

            .accent-strip {
                height: 3px;
                background: linear-gradient(90deg, #6C63FF 0%, #A855F7 45%, #EC4899 75%, #F59E0B 100%);
            }

            .card-inner { padding: 2.5rem 2.25rem; }

            /* ─── FOOTER ─── */
            .page-footer {
                margin-top: 1.75rem;
                text-align: center;
                font-size: .78rem;
                color: var(--text-muted);
                animation: fadeUp .65s cubic-bezier(.16,1,.3,1) .12s both;
            }
            .page-footer a { color: var(--accent-light); text-decoration:none; transition: color .2s; }
            .page-footer a:hover { color:#fff; }
            .footer-divider { border:none; border-top:1px solid rgba(255,255,255,.07); margin:.9rem 0; }

            /* ─── GLOBAL FORM STYLES (shared by login + register) ─── */
            .auth-heading { font-family:'Syne',sans-serif; font-size:1.65rem; font-weight:700; color:var(--text); line-height:1.2; }
            .auth-subheading { font-size:.875rem; color:var(--text-muted); margin-top:.4rem; }
            .form-group { margin-top:1.25rem; }
            .form-label { display:block; font-size:.825rem; font-weight:500; color:var(--text); margin-bottom:.45rem; letter-spacing:.01em; }
            .input-wrap { position:relative; }
            .input-icon {
                position:absolute; left:12px; top:50%; transform:translateY(-50%);
                width:18px; height:18px; color:rgba(255,255,255,.35); pointer-events:none;
            }
            .form-input {
                width:100%; padding:.7rem .9rem .7rem 2.5rem;
                background: var(--input-bg);
                border: 1px solid var(--input-border);
                border-radius: 12px;
                color: var(--text);
                font-family: 'DM Sans', sans-serif;
                font-size: .9rem;
                outline: none;
                transition: border-color .22s, box-shadow .22s, background .22s;
                -webkit-text-fill-color: var(--text);
            }
            .form-input::placeholder { color: rgba(255,255,255,.28); }
            .form-input:-webkit-autofill { -webkit-box-shadow: 0 0 0 100px #1A1840 inset; -webkit-text-fill-color: var(--text); }
            .form-input:focus {
                border-color: var(--accent);
                background: rgba(108,99,255,.10);
                box-shadow: 0 0 0 3px rgba(108,99,255,.22), 0 2px 8px rgba(0,0,0,.2);
            }
            .form-error { font-size:.78rem; color:var(--error); margin-top:.35rem; }
            .form-hint  { font-size:.76rem; color:var(--text-muted); margin-top:.35rem; }

            .btn-primary {
                width:100%; padding:.78rem 1rem;
                background: linear-gradient(135deg, var(--accent) 0%, #8B5CF6 100%);
                color:#fff; font-family:'Syne',sans-serif; font-weight:600;
                font-size:.9rem; letter-spacing:.02em;
                border:none; border-radius:12px; cursor:pointer;
                box-shadow: 0 4px 20px rgba(108,99,255,.45);
                transition: transform .2s, box-shadow .2s, filter .2s;
                display:flex; align-items:center; justify-content:center; gap:.5rem;
            }
            .btn-primary:hover { transform:translateY(-1px); box-shadow:0 8px 28px rgba(108,99,255,.55); filter:brightness(1.08); }
            .btn-primary:active { transform:translateY(0); filter:brightness(.96); }

            .link-inline { color:var(--accent-light); text-decoration:none; font-weight:500; transition:color .2s; }
            .link-inline:hover { color:#fff; }

            .divider { border:none; border-top:1px solid rgba(255,255,255,.09); margin:1.5rem 0; }

            .remember-label { display:flex; align-items:center; gap:.5rem; cursor:pointer; font-size:.85rem; color:var(--text-muted); }
            .remember-label input[type="checkbox"] {
                width:16px; height:16px; accent-color:var(--accent);
                border-radius:4px; cursor:pointer;
            }
            .forgot-link { font-size:.82rem; color:var(--accent-light); text-decoration:none; transition:color .2s; }
            .forgot-link:hover { color:#fff; }

            .row-between { display:flex; align-items:center; justify-content:space-between; }
            .text-center { text-align:center; }
            .mt-4 { margin-top:1rem; }
            .mt-5 { margin-top:1.4rem; }
            .small-text { font-size:.82rem; color:var(--text-muted); }
        </style>
    </head>
    <body>
        <!-- Background scene -->
        <div class="scene" aria-hidden="true">
            <div class="scene-img"></div>
            <div class="scene-overlay"></div>
            <div class="scene-grid"></div>
            <div class="orb orb-a"></div>
            <div class="orb orb-b"></div>
            <div class="orb orb-c"></div>
        </div>

        <div class="page">
            <div class="card-wrap">
                <!-- Logo -->
                <a href="/" class="logo-area">
                    <div class="logo-badge">
                        <x-application-logo />
                    </div>
                    <span class="brand">{{ config('app.name', 'App') }}</span>
                </a>

                <!-- Card -->
                <div class="auth-card">
                    <div class="accent-strip"></div>
                    <div class="card-inner">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer -->
                <div class="page-footer">
                    <hr class="footer-divider">
                    <p>
                        By continuing, you agree to our
                        <a href="#">Terms of Service</a> &amp; <a href="#">Privacy Policy</a>
                    </p>
                    <p style="margin-top:.5rem">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </body>
</html>
