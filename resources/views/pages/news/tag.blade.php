{{-- resources/views/pages/news/tag.blade.php --}}
@extends('layouts.app')

@section('title', '#' . $tag->name . ' — Kusoma')
@section('meta_description', 'Browse all articles tagged ' . $tag->name . ' on Kusoma.')

@section('content')

{{-- ── Tag hero ──────────────────────────────────────────────────────── --}}
<div class="bg-slate-900 border-b border-slate-800">
  <div class="max-w-6xl mx-auto px-6 py-12">
    <p class="text-xs font-bold uppercase tracking-widest text-emerald-400 mb-2">Tag archive</p>
    <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-2">
      #{{ $tag->name }}
    </h1>
    <p class="text-slate-400 text-sm">
      {{ $posts->total() }} {{ Str::plural('article', $posts->total()) }} tagged with this topic
    </p>
  </div>
</div>

{{-- ── Posts grid ───────────────────────────────────────────────────── --}}
<main class="max-w-6xl mx-auto px-6 py-10">

  @if($posts->isEmpty())
    <div class="text-center py-20 text-slate-400">
      <i class="fas fa-tag text-4xl mb-3 block"></i>
      <p class="text-sm font-medium">No published articles under this tag yet.</p>
      <a href="{{ route('news.index') }}" class="mt-4 inline-block text-emerald-600 hover:underline text-sm font-semibold">
        ← Back to news
      </a>
    </div>
  @else

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($posts as $post)
      <a
        href="{{ route('post.show', $post->slug) }}"
        class="group bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col"
      >
        {{-- Thumbnail --}}
        <div class="h-44 bg-slate-100 overflow-hidden relative shrink-0">
          @if($post->featured_image)
            <img
              src="{{ asset($post->featured_image) }}"
              class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
              alt="{{ $post->title }}"
            />
          @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
              <i class="fas fa-newspaper text-slate-300 text-3xl"></i>
            </div>
          @endif

          {{-- Category badge --}}
          @if($post->category)
          <span class="absolute top-3 left-3 bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
            {{ $post->category->name }}
          </span>
          @endif
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col flex-1">
          <h2 class="font-bold text-sm text-slate-900 leading-snug group-hover:text-emerald-700 transition mb-2 flex-1">
            {{ Str::limit($post->title, 90) }}
          </h2>

          <div class="flex items-center justify-between text-xs text-slate-400 mt-3 pt-3 border-t border-slate-50">
            <span class="font-medium text-emerald-600">{{ $post->author->name ?? 'Admin' }}</span>
            <span>{{ $post->published_at?->format('M j, Y') }}</span>
          </div>
        </div>
      </a>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($posts->hasPages())
    <div class="mt-10 flex justify-center">
      {{ $posts->links() }}
    </div>
    @endif

  @endif
</main>
@endsection
