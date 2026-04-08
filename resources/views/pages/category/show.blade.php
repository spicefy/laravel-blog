{{-- resources/views/pages/category/show.blade.php --}}
@extends('layouts.app')

@php
    $metaDescription = $category->meta_description ?? "Browse all articles in {$category->name} category";
@endphp

@section('title', "{$category->name} - Category")
@section('meta_description', $metaDescription)
@section('og_title', $category->name)
@section('og_description', $metaDescription)

@section('content')
<main class="max-w-6xl mx-auto px-6 py-8">
    

  {{-- Category Header --}}
  <div class="text-center mb-12">
    <div class="inline-block p-3 bg-{{ $category->css_suffix ?? 'kgreen' }}/10 rounded-full mb-4">
      <i class="{{ $category->icon ?? 'fas fa-folder' }} text-3xl text-{{ $category->css_suffix ?? 'kgreen' }}"></i>
    </div>
    <h1 class="font-display font-semibold text-4xl md:text-5xl text-navy mb-3">
      {{ $category->name }}
    </h1>
    @if($category->description)
      <p class="text-lg text-muted max-w-2xl mx-auto">{{ $category->description }}</p>
    @endif
  </div>

  {{-- Posts Grid --}}
  @if($posts->isNotEmpty())
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($posts as $post)
        <article class="bg-white border border-kborder rounded-xl overflow-hidden hover:shadow-lg transition">
          @if($post->featured_image)
            <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" 
                 class="w-full h-48 object-cover">
          @else
            <div class="w-full h-48 bg-gradient-to-br from-navy to-royal flex items-center justify-center text-white">
              <i class="fas fa-newspaper text-4xl opacity-50"></i>
            </div>
          @endif
          
          <div class="p-5">
            <div class="flex items-center gap-2 text-xs text-muted mb-3">
              <time datetime="{{ $post->published_at?->toIso8601String() }}">
                {{ $post->published_at?->format('M j, Y') }}
              </time>
              <span>•</span>
              <span>{{ $post->reading_time ?? 3 }} min read</span>
            </div>
            
            <h2 class="font-display font-semibold text-xl text-navy mb-2 hover:text-royal transition">
              <a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a>
            </h2>
            
            <p class="text-muted text-sm mb-4">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 120) }}</p>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2 text-sm text-muted">
                <i class="fas fa-user"></i>
                <span>{{ $post->author->name ?? 'Admin' }}</span>
              </div>
              <a href="{{ route('post.show', $post->slug) }}" class="text-royal hover:underline text-sm">
                Read more →
              </a>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-12">
      {{ $posts->links() }}
    </div>
  @else
    <div class="text-center py-16">
      <i class="fas fa-inbox text-6xl text-muted mb-4"></i>
      <p class="text-muted text-lg">No articles found in this category yet.</p>
    </div>
  @endif

</main>
@endsection