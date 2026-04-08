{{-- resources/views/pages/author/show.blade.php --}}
@extends('layouts.app')

@section('title', $author->name . ' – Kusoma')
@section('meta_description', 'Read all articles by ' . $author->name . ' on Kusoma — education and technology news from Kenya and Africa.')
@section('canonical', url("/author/{$author->id}"))

@section('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ProfilePage",
  "name": "{{ addslashes($author->name) }}",
  "url": "{{ url('/author/' . $author->id) }}",
  "mainEntity": {
    "@type": "Person",
    "name": "{{ addslashes($author->name) }}",
    "description": "{{ addslashes($author->bio ?? '') }}"
  }
}
</script>
@endsection

@section('content')
<main class="max-w-5xl mx-auto px-6 py-8">

  {{-- Breadcrumb --}}
  <nav aria-label="Breadcrumb" class="text-xs text-muted mb-5 flex items-center gap-1.5">
    <a href="{{ route('home') }}" class="hover:text-royal">Home</a>
    <i class="fas fa-chevron-right text-[10px]"></i>
    <span>{{ $author->name }}</span>
  </nav>

  {{-- Author card --}}
  <div class="bg-white border border-kborder rounded-xl p-6 mb-8 flex flex-col sm:flex-row items-start gap-6">
    <img src="https://ui-avatars.com/api/?name={{ urlencode($author->name) }}&background=2755c8&color=fff&size=80"
         class="w-20 h-20 rounded-full shrink-0" alt="{{ $author->name }}"
         width="80" height="80" />
    <div>
      <h1 class="font-display font-semibold text-2xl text-navy mb-1">{{ $author->name }}</h1>
      @if($author->bio)
        <p class="text-sm text-muted leading-relaxed mb-3">{{ $author->bio }}</p>
      @endif
      <div class="flex flex-wrap gap-3 text-sm text-muted">
        <span><i class="fas fa-newspaper mr-1 text-xs"></i>{{ $posts->total() }} articles</span>
        @if($author->twitter)
          <a href="https://twitter.com/{{ $author->twitter }}" target="_blank" rel="noopener"
             class="text-royal hover:underline flex items-center gap-1">
            <i class="fab fa-twitter text-xs"></i> @{{ $author->twitter }}
          </a>
        @endif
      </div>
    </div>
  </div>

  {{-- Posts grid --}}
  <div class="flex items-center gap-4 mb-5">
    <h2 class="font-display font-semibold text-xl text-navy shrink-0">
      Articles by {{ $author->name }}
    </h2>
    <div class="flex-1 h-px bg-kborder"></div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    @forelse($posts as $post)
    <article class="bg-white border border-kborder rounded-xl overflow-hidden hover:shadow-sm transition-shadow">
      @if($post->featured_image)
        <a href="{{ route('post.show', $post->slug) }}">
          <img src="{{ asset($post->featured_image) }}"
               alt="{{ $post->title }}"
               class="w-full h-40 object-cover"
               width="400" height="160" loading="lazy" />
        </a>
      @else
        <div class="w-full h-3 bg-{{ $post->category->css_suffix }}"></div>
      @endif
      <div class="p-4">
        <a href="{{ route('category.show', $post->category->slug) }}"
           class="text-[11px] font-semibold uppercase tracking-wide text-{{ $post->category->css_suffix }} mb-1.5 block">
          {{ $post->category->name }}
        </a>
        <h3 class="font-medium text-kgreen leading-snug mb-2">
          <a href="{{ route('post.show', $post->slug) }}" class="hover:underline">
            {{ $post->title }}
          </a>
        </h3>
        <div class="flex gap-2 text-xs text-muted">
          <time datetime="{{ $post->published_at->toIso8601String() }}">
            {{ $post->published_at->format('M j, Y') }}
          </time>
          <span>·</span>
          <span>{{ $post->reading_time }} min read</span>
        </div>
      </div>
    </article>
    @empty
    <p class="col-span-3 text-muted text-center py-12">No published articles yet.</p>
    @endforelse
  </div>

  {{ $posts->links() }}

</main>
@endsection