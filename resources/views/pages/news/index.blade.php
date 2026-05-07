@extends('layouts.app')

@section('title', 'Latest Education & Tech News in Kenya')
@section('meta_description', 'Breaking education news, KCSE results, university updates and tech stories across Kenya and Africa.')

@section('content')

{{-- ── Page hero ─────────────────────────────────────────────────────── --}}
<div class="bg-slate-900 border-b border-slate-800">
  <div class="max-w-6xl mx-auto px-6 py-12 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-emerald-400 mb-2">Kusoma News</p>
      <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight tracking-tight">
        Education &amp; Tech in Kenya
      </h1>
      <p class="text-slate-400 text-sm mt-2 max-w-lg leading-relaxed">
        Breaking KCSE results, university updates, scholarship opportunities and tech stories across Kenya and Africa.
      </p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
      <span class="inline-flex items-center gap-1.5 bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 text-xs font-semibold px-3 py-1.5 rounded-full">
        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
        Live updates
      </span>
    </div>
  </div>
</div>

<main class="max-w-6xl mx-auto px-6 py-10">

  {{-- ── Categories Section ──────────────────────────────────────────── --}}
  <div class="flex items-center gap-4 mb-7">
    <h2 class="font-extrabold text-xl text-slate-900 shrink-0">Browse Categories</h2>
    <div class="flex-1 h-px bg-slate-100"></div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-14">
    @foreach($categories as $category)
    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col">

      {{-- Card header --}}
      <div class="bg-{{ $category->css_suffix ?? 'emerald-700' }} px-5 py-4 flex items-center justify-between">
        <h3 class="text-sm font-bold text-white flex items-center gap-2">
          <span class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
            <i class="{{ $category->icon ?? 'fas fa-folder' }} text-white text-xs"></i>
          </span>
          <a href="{{ route('category.show', $category->slug) }}" class="hover:underline underline-offset-2">
            {{ $category->name }}
          </a>
        </h3>
        @if($category->posts_count > 0)
        <span class="text-[11px] font-bold bg-white/20 text-white rounded-full px-2.5 py-0.5 shrink-0">
          {{ $category->posts_count }}
        </span>
        @endif
      </div>

      {{-- Posts list --}}
      <ul class="divide-y divide-slate-50 flex-1">
        @foreach($category->publishedPosts as $post)
        <li class="px-5 py-3.5 hover:bg-slate-50 transition-colors group">
          <a
            href="{{ route('post.show', $post->slug) }}"
            class="text-[13.5px] font-semibold text-slate-800 group-hover:text-emerald-700 leading-snug block mb-1.5 transition-colors"
          >
            {{ $post->title }}
          </a>
          <div class="flex items-center gap-2 text-xs text-slate-400">
            <span class="font-medium text-emerald-600">{{ $post->author->name ?? 'Admin' }}</span>
            <span class="text-slate-200">•</span>
            <span>{{ $post->published_at->format('M j, Y') }}</span>
            @if($post->reading_time)
            <span class="text-slate-200">•</span>
            <span>{{ $post->reading_time }} min</span>
            @endif
          </div>
        </li>
        @endforeach
      </ul>

      {{-- Footer link --}}
      <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
        <a
          href="{{ route('category.show', $category->slug) }}"
          class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:underline underline-offset-2 transition flex items-center gap-1"
        >
          All {{ strtolower($category->name) }} articles
          <i class="fas fa-arrow-right text-[10px]"></i>
        </a>
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── Recent Posts Section ────────────────────────────────────────── --}}
  <div class="flex items-center gap-4 mb-7">
    <h2 class="font-extrabold text-xl text-slate-900 shrink-0">Recently Published</h2>
    <div class="flex-1 h-px bg-slate-100"></div>
  </div>

  <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm">

    {{-- Table header --}}
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
      <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
      <span class="text-sm font-bold text-slate-700">Newest articles</span>
    </div>

    <div class="divide-y divide-slate-50">
      @foreach($recentPosts as $index => $post)
      <div class="flex items-start gap-4 px-6 py-4 hover:bg-slate-50/70 transition-colors group">

        {{-- Index number --}}
        <span class="text-2xl font-extrabold text-slate-400 tabular-nums leading-none mt-0.5 w-7 shrink-0 select-none">
          {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
        </span>

        <div class="flex-1 min-w-0">
          <a
            href="{{ route('post.show', $post->slug) }}"
            class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700 leading-snug block mb-1.5 transition-colors"
          >
          
            {{ $post->title }}
          </a>
          <div class="flex items-center flex-wrap gap-2 text-xs text-slate-400">
            <span class="font-semibold text-emerald-600">{{ $post->author->name ?? 'Admin' }}</span>
            <span class="text-slate-200">•</span>
            <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-medium">
              {{ $post->category->name ?? 'Uncategorized' }}
            </span>
            @if($post->reading_time)
            <span class="text-slate-200">•</span>
            <span>{{ $post->reading_time }} min read</span>
            
            @endif
          </div>
        </div>

        <time
          class="text-xs text-slate-400 shrink-0 tabular-nums mt-0.5"
          datetime="{{ $post->published_at->toIso8601String() }}"
        >
          {{ $post->published_at->diffForHumans() }}
        </time>
          
        <svg class="arrow-icon w-4 h-4 text-slate-800 group-hover:text-emerald-700 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
        </svg>
      </a>

      </div>
      @endforeach
    </div>

  </div>

</main>
@endsection
