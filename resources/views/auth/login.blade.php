<x-guest-layout>
    {{-- ── SESSION STATUS ── --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- ── HEADER ── --}}
    <div class="text-center" style="margin-bottom:1.75rem;">
        <div style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:14px;background:rgba(108,99,255,.18);margin-bottom:.9rem;">
            <svg style="width:22px;height:22px;color:var(--accent-light)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="auth-heading">Welcome back</h1>
        <p class="auth-subheading">Sign in to your account to continue</p>
    </div>

    {{-- ── FORM ── --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Email address</label>
            <div class="input-wrap">
                <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <x-text-input
                    id="email"
                    class="form-input"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="form-error" />
        </div>

        {{-- Password --}}
        <div class="form-group">
            <div class="row-between" style="margin-bottom:.45rem;">
                <label for="password" class="form-label" style="margin-bottom:0">Password</label>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <div class="input-wrap" id="pwd-wrap">
                <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <x-text-input
                    id="password"
                    class="form-input"
                    style="padding-right:2.8rem"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <button type="button" id="toggle-pwd" onclick="togglePassword('password','toggle-pwd')"
                    style="position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:rgba(255,255,255,.38);padding:2px;transition:color .2s"
                    aria-label="Toggle password visibility">
                    <svg id="toggle-pwd-eye" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="form-error" />
        </div>

        {{-- Remember me --}}
        <div class="form-group">
            <label class="remember-label" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember me for 30 days</span>
            </label>
        </div>

        {{-- Submit --}}
        <div style="margin-top:1.6rem;">
            <button type="submit" class="btn-primary">
                <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Sign in
            </button>
        </div>
    </form>

    {{-- Register link --}}
    <hr class="divider">
    <p class="text-center small-text">
        Don't have an account?
        <a href="{{ route('register') }}" class="link-inline">Create one free →</a>
    </p>

    <script>
        function togglePassword(inputId, btnId) {
            const input = document.getElementById(inputId);
            const btn   = document.getElementById(btnId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.style.color = isHidden ? 'var(--accent-light)' : 'rgba(255,255,255,.38)';
        }
    </script>
</x-guest-layout>
