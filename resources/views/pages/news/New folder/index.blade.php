@extends('layouts.app')

@section('title', 'Latest Education & Tech News in Kenya')
@section('meta_description', 'Breaking education news, KCSE results, university updates and tech stories across Kenya and Africa.')

@section('content')
<main class="max-w-6xl mx-auto px-6 py-8">

  <h1 class="sr-only">Latest Education News in Kenya – Kusoma</h1>

  {{-- Categories Section --}}
  <div class="flex items-center gap-4 mb-6">
    <h2 class="font-display font-semibold text-xl text-navy shrink-0">Categories</h2>
    <div class="flex-1 h-px bg-kborder"></div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
    @foreach($categories as $category)
    <div class="bg-white border border-kborder rounded-xl overflow-hidden">
      <div class="bg-{{ $category->css_suffix ?? 'kgreen' }} px-4 py-3.5 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-white flex items-center gap-2">
          <i class="{{ $category->icon ?? 'fas fa-folder' }} text-white/75 text-xs"></i>
          <a href="{{ route('category.show', $category->slug) }}" class="hover:underline">
            {{ $category->name }}
          </a>
        </h3>
        @if($category->posts_count > 0)
        <span class="text-[11px] font-semibold bg-white/20 text-white rounded-full px-2.5 py-0.5">
          {{ $category->posts_count }} posts
        </span>
        @endif
      </div>
      
      <ul class="divide-y divide-kborder">
        @foreach($category->publishedPosts as $post)
        <li class="px-4 py-3 hover:bg-kbg transition-colors">
          <a href="{{ route('post.show', $post->slug) }}"
             class="text-[13.5px] font-medium text-kgreen leading-snug block mb-1.5 hover:underline">
            {{ $post->title }}
          </a>
          <div class="flex gap-3 text-xs text-muted">
            <span class="text-royal font-medium">By {{ $post->author->name ?? 'Admin' }}</span>
            <span>•</span>
            <span>{{ $post->published_at->format('M j, Y') }}</span>
            @if($post->reading_time)
            <span>•</span>
            <span>{{ $post->reading_time }} min read</span>
            @endif
          </div>
        </li>
        @endforeach
      </ul>
      
      <div class="px-4 py-2.5 border-t border-kborder bg-kbg text-right">
        <a href="{{ route('category.show', $category->slug) }}" class="text-xs font-medium text-royal hover:underline">
          All {{ strtolower($category->name) }} articles →
        </a>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Recent Posts Section --}}
  <div class="flex items-center gap-4 mb-6">
    <h2 class="font-display font-semibold text-xl text-navy shrink-0">Recently published</h2>
    <div class="flex-1 h-px bg-kborder"></div>
  </div>

  <div class="bg-white border border-kborder rounded-xl overflow-hidden">
    <div class="px-5 py-3.5 border-b border-kborder bg-kbg">
      <div class="flex items-center gap-2 text-sm font-semibold text-gray-800">
        <i class="fas fa-clock text-muted text-xs"></i>
        <span>Newest articles</span>
      </div>
    </div>
    
    <div class="divide-y divide-kborder">
      @foreach($recentPosts as $post)
      <div class="flex justify-between items-start gap-3 px-5 py-3 hover:bg-kbg transition-colors">
        <div class="flex-1">
          <a href="{{ route('post.show', $post->slug) }}"
             class="text-[13.5px] font-medium text-kgreen hover:underline leading-snug">
            {{ $post->title }}
          </a>
          <div class="flex gap-3 text-xs text-muted mt-1">
            <span class="text-royal">By {{ $post->author->name ?? 'Admin' }}</span>
            <span>•</span>
            <span>{{ $post->category->name ?? 'Uncategorized' }}</span>
            @if($post->reading_time)
            <span>•</span>
            <span>{{ $post->reading_time }} min read</span>
            @endif
          </div>
        </div>
        <time class="text-xs text-muted shrink-0" datetime="{{ $post->published_at->toIso8601String() }}">
          {{ $post->published_at->diffForHumans() }}
        </time>
      </div>
      @endforeach
    </div>
  </div>

</main>
@endsection