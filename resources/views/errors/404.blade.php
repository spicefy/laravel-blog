<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>404 – Page Not Found</title>
  
 <!-- Laravel Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  
  
</head>
<body class="min-h-screen bg-green-400 flex flex-col font-inter text-blue-900 relative">

  <!-- Main content -->
  <main class="flex-1 flex flex-col items-center justify-center px-4 py-20 relative z-10">

    <!-- Logo -->
    <div class="opacity-0-init animate-fade-up mb-12">
      <svg width="48" height="32" viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M6 24C10 18 14 8 24 8C34 8 38 18 42 24" stroke="#818cf8" stroke-width="4" stroke-linecap="round" fill="none"/>
        <path d="M2 18C6 12 11 4 24 4C37 4 42 12 46 18" stroke="#818cf8" stroke-width="3.5" stroke-linecap="round" fill="none" opacity="0.5"/>
      </svg>
    </div>

    <!-- 404 label -->
    <p class="opacity-0-init animate-fade-up-d1 text-indigo-400 text-sm font-sora font-semibold tracking-widest uppercase mb-3">
      404
    </p>

    <!-- Heading -->
    <h1 class="opacity-0-init animate-fade-up-d2 font-sora font-extrabold text-4xl md:text-5xl text-indigo-400 text-center leading-tight mb-4">
      This page does not exist
    </h1>

    <!-- Subtext -->
    <p class="opacity-0-init animate-fade-up-d3 text-blue-400 text-base text-center mb-12 max-w-sm">
      Sorry, we couldn't find the page you're looking for.
    </p>

    <!-- Link rows -->
    <div class="opacity-0-init animate-fade-up-d4 w-full max-w-lg border border-white/10 rounded-xl overflow-hidden divide-y divide-white/10">

      <!-- Documentation -->
      <a href="#" class="link-row flex items-center gap-4 px-5 py-4 group">
        <div class="icon-box w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-sora font-semibold text-sm text-indigo-400">Documentation</p>
          <p class="text-blue-400 text-xs mt-0.5">Learn how to integrate our tools with your app.</p>
        </div>
        <svg class="arrow-icon w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
        </svg>
      </a>

      <!-- API Reference -->
      <a href="#" class="link-row flex items-center gap-4 px-5 py-4 group">
        <div class="icon-box w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
            <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
            <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-sora font-semibold text-sm text-white">API Reference</p>
          <p class="text-slate-400 text-xs mt-0.5">A complete API reference for our libraries.</p>
        </div>
        <svg class="arrow-icon w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
        </svg>
      </a>

      <!-- Guides -->
      <a href="#" class="link-row flex items-center gap-4 px-5 py-4 group">
        <div class="icon-box w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-sora font-semibold text-sm text-white">Guides</p>
          <p class="text-slate-400 text-xs mt-0.5">Installation guides that cover popular setups.</p>
        </div>
        <svg class="arrow-icon w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
        </svg>
      </a>

      <!-- Blog -->
      <a href="#" class="link-row flex items-center gap-4 px-5 py-4 group">
        <div class="icon-box w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-sora font-semibold text-sm text-white">Blog</p>
          <p class="text-slate-400 text-xs mt-0.5">Read our latest news and articles.</p>
        </div>
        <svg class="arrow-icon w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
        </svg>
      </a>

    </div>

    <!-- Back to home -->
    <a href="/" class="opacity-0-init animate-fade-up-d5 mt-8 text-indigo-400 text-sm font-medium font-sora hover:text-indigo-300 transition-colors flex items-center gap-1.5 group">
      <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
      Back to home
    </a>

  </main>

  <!-- Footer -->
  <footer class="opacity-0-init animate-fade-up-d6 relative z-10 border-t border-white/10 py-6 px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <p class="text-slate-500 text-xs">© Your Company, Inc. All rights reserved.</p>
    <div class="flex items-center gap-5">
      <!-- X / Twitter -->
      <a href="#" class="text-slate-500 hover:text-slate-300 transition-colors">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
          <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.74l7.73-8.835L1.254 2.25H8.08l4.256 5.634 5.908-5.634Zm-1.161 17.52h1.833L7.084 4.126H5.117Z"/>
        </svg>
      </a>
      <!-- GitHub -->
      <a href="#" class="text-slate-500 hover:text-slate-300 transition-colors">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/>
        </svg>
      </a>
      <!-- Instagram -->
      <a href="#" class="text-slate-500 hover:text-slate-300 transition-colors">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
          <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
          <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
        </svg>
      </a>
    </div>
  </footer>

</body>
</html>