{{-- resources/views/pages/category/show.blade.php --}}
@extends('layouts.app')

@section('title', $category->meta_title ?? 'Latest ' . $category->name . ' News in Kenya')
@section('meta_description', $category->meta_description ?? 'Read the latest ' . strtolower($category->name) . ' news and analysis from across Kenya and Africa on Kusoma.')
@section('canonical', route('category.show', $category->slug))

@section('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "{{ $category->name }} – Kusoma",
  "description": "{{ $category->meta_description ?? 'Latest ' . $category->name . ' news in Kenya' }}",
  "url": "{{ route('category.show', $category->slug) }}"
}
</script>
@endsection

@section('content')
<main class="max-w-5xl mx-auto px-6 py-8">

  {{-- Breadcrumbs --}}
  <nav aria-label="Breadcrumb" class="text-xs text-muted mb-5 flex items-center gap-1.5">
    <a href="{{ route('news.index') }}" class="hover:text-royal">Home</a>
    <i class="fas fa-chevron-right text-[10px]"></i>
    <span>{{ $category->name }}</span>
  </nav>

  {{-- H1 --}}
  <div class="flex items-center gap-4 mb-8">
    <div class="w-2 h-10 bg-{{ $category->css_suffix }} rounded-full"></div>
    <div>
      <h1 class="font-display font-semibold text-3xl text-navy">{{ $category->name }}</h1>
      @if($category->description)
        <p class="text-muted text-sm mt-0.5">{{ $category->description }}</p>
      @endif
    </div>
  </div>

  {{-- Articles grid --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    @forelse($posts as $post)
    <article class="bg-white border border-kborder rounded-xl overflow-hidden hover:shadow-sm transition-shadow">
      @if($post->featured_image)
        <a href="{{ route('post.show', $post->slug) }}">
          <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}"
               class="w-full h-40 object-cover" width="400" height="160" loading="lazy" />
        </a>
      @endif
      <div class="p-4">
        <a href="{{ route('post.show', $post->slug) }}"
           class="font-medium text-kgreen hover:underline block mb-2 leading-snug">
          {{ $post->title }}
        </a>
        @if($post->excerpt)
          <p class="text-xs text-muted line-clamp-2 mb-3">{{ $post->excerpt }}</p>
        @endif
        <div class="flex gap-2 text-xs text-muted">
          <span class="text-royal font-medium">{{ $post->author->name }}</span>
          <span>•</span>
          <time datetime="{{ $post->published_at->toIso8601String() }}">
            {{ $post->published_at->format('M j, Y') }}
          </time>
          <span>•</span>
          <span>{{ $post->reading_time }} min read</span>
        </div>
      </div>
    </article>
    @empty
      <p class="col-span-3 text-muted text-center py-12">No articles in this category yet.</p>
    @endforelse
  </div>

  {{-- Pagination --}}
  {{ $posts->links() }}

</main>
@endsection
