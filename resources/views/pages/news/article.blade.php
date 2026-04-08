{{-- resources/views/pages/news/article.blade.php --}}
@extends('layouts.app')

{{-- ── SEO meta ────────────────────────────────────────────────────────────── --}}
@section('title', $post->meta_title ?? $post->title)
@section('meta_description', $post->meta_description ?? Str::limit($post->excerpt ?? $post->content, 155))
@section('meta_keywords', $post->meta_keywords ?? '')
@section('canonical', url()->current())

{{-- Open Graph --}}
@section('og_type', 'article')
@section('og_title', $post->title)
@section('og_description', $post->meta_description ?? Str::limit($post->excerpt ?? $post->content, 155))
@section('og_image', $post->featured_image ? asset($post->featured_image) : asset('images/default-og.jpg'))

{{-- ── Google NewsArticle structured data ─────────────────────────────────── --}}
@section('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "{{ addslashes($post->title) }}",
  "description": "{{ addslashes($post->meta_description ?? Str::limit($post->excerpt ?? $post->content, 155)) }}",
  "image": ["{{ $post->featured_image ? asset($post->featured_image) : asset('images/default-og.jpg') }}"],
  "datePublished": "{{ $post->published_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": {
    "@type": "Person",
    "name": "{{ $post->author->name ?? 'Admin' }}",
    "url": "{{ url('/author/' . ($post->author->id ?? '1')) }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Kusoma",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  },
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  },
  "articleSection": "{{ $post->category->name ?? 'Uncategorized' }}",
  "keywords": "{{ $post->meta_keywords ?? '' }}"
}
</script>

{{-- ── BreadcrumbList ──────────────────────────────────────────────────────── --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type":"ListItem","position":1,"name":"Home","item":"{{ url('/news') }}"},
    {"@type":"ListItem","position":2,"name":"{{ $post->category->name ?? 'News' }}","item":"{{ route('category.show', $post->category->slug ?? 'news') }}"},
    {"@type":"ListItem","position":3,"name":"{{ addslashes($post->title) }}","item":"{{ url()->current() }}"}
  ]
}
</script>
@endsection

@section('content')
<main class="max-w-4xl mx-auto px-6 py-8">

  {{-- ── Breadcrumbs (visible + SEO) ────────────────────────────────── --}}
  <nav aria-label="Breadcrumb" class="text-xs text-muted mb-5 flex items-center gap-1.5">
    <a href="{{ route('news.index') }}" class="hover:text-royal">Home</a>
    <i class="fas fa-chevron-right text-[10px]"></i>
    <a href="{{ route('category.show', $post->category->slug ?? '#') }}" class="hover:text-royal">
      {{ $post->category->name ?? 'News' }}
    </a>
    <i class="fas fa-chevron-right text-[10px]"></i>
    <span class="text-gray-600 truncate max-w-[200px]">{{ $post->title }}</span>
  </nav>

  {{-- ── Article header ───────────────────────────────────────────────── --}}
  <div class="mb-6">
    <div class="flex items-center gap-2 text-xs text-muted mb-3">
      <a href="{{ route('category.show', $post->category->slug ?? '#') }}"
         class="bg-{{ $post->category->css_suffix ?? 'kgreen' }}/10 text-{{ $post->category->css_suffix ?? 'kgreen' }} rounded-full px-3 py-1 font-medium uppercase tracking-wide hover:opacity-80 transition">
        {{ $post->category->name ?? 'Uncategorized' }}
      </a>
      <span>
        <i class="far fa-clock mr-1"></i> 
        {{ $post->reading_time ?? 3 }} min read ·
        <time datetime="{{ $post->published_at->toIso8601String() }}">
          {{ $post->published_at->format('M j, Y') }}
        </time>
      </span>
    </div>

    {{-- H1 — the single most important on-page SEO element --}}
    <h1 class="font-display font-semibold text-4xl md:text-5xl text-navy leading-tight mb-4">
      {{ $post->title }}
    </h1>

    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-3">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->author->name ?? 'Admin') }}&background=2755c8&color=fff&size=48"
             class="w-12 h-12 rounded-full" alt="{{ $post->author->name ?? 'Admin' }}" width="48" height="48" />
        <div>
          <p class="font-medium text-gray-900">{{ $post->author->name ?? 'Admin' }}</p>
          <p class="text-xs text-muted">{{ $post->author->bio ?? 'Staff writer' }}</p>
        </div>
      </div>
      <div class="flex gap-2">
        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
           target="_blank" rel="noopener"
           class="border border-kborder rounded-lg px-4 py-2 text-sm font-medium text-muted hover:bg-kbg transition-colors">
          <i class="fab fa-twitter mr-2"></i>Tweet
        </a>
        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}"
           target="_blank" rel="noopener"
           class="border border-kborder rounded-lg px-4 py-2 text-sm font-medium text-muted hover:bg-kbg transition-colors">
          <i class="fab fa-linkedin mr-2"></i>Share
        </a>
        <button class="border border-kborder rounded-lg px-4 py-2 text-sm font-medium text-royal hover:bg-klight transition-colors">
          <i class="far fa-bookmark mr-2"></i>Save
        </button>
      </div>
    </div>
  </div>

  {{-- ── Featured image ───────────────────────────────────────────────── --}}
  @if($post->featured_image)
    <figure class="mb-8">
      <img src="{{ asset($post->featured_image) }}"
           alt="{{ $post->title }}"
           class="w-full h-80 object-cover rounded-xl"
           width="800" height="320"
           loading="eager" />
    </figure>
  @else
    <div class="w-full h-80 bg-gradient-to-br from-navy to-royal rounded-xl mb-8 flex items-center justify-center text-white">
      <span class="text-lg font-display">{{ $post->category->name ?? 'News' }}</span>
    </div>
  @endif

  {{-- ── Article body ─────────────────────────────────────────────────── --}}
  <article class="prose prose-lg max-w-none text-gray-800">
    @if($post->excerpt)
      <p class="lead font-display text-xl text-navy/80 mb-6">{{ $post->excerpt }}</p>
    @endif
    {!! $post->content !!}
  </article>

  {{-- ── Tags (internal linking via tag pages) ──────────────────────── --}}
  @if(isset($post->tags) && $post->tags->isNotEmpty())
  <div class="border-t border-kborder mt-10 pt-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-wrap gap-2 items-center">
      <span class="text-xs font-medium text-muted">Tags:</span>
      @foreach($post->tags as $tag)
        <a href="{{ route('tag.show', $tag->slug) }}"
           class="text-xs bg-klight text-royal px-3 py-1 rounded-full hover:bg-royal hover:text-white transition-colors">
          {{ $tag->name }}
        </a>
      @endforeach
    </div>
    <div class="flex gap-3 text-muted text-sm">
      <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="hover:text-royal"><i class="fab fa-twitter"></i></a>
      <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="hover:text-royal"><i class="fab fa-linkedin"></i></a>
      <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode(url()->current()) }}" class="hover:text-royal"><i class="far fa-envelope"></i></a>
    </div>
  </div>
  @endif

  {{-- ── Author bio ────────────────────────────────────────────────────── --}}
  <div class="bg-white border border-kborder rounded-xl p-6 mt-8 flex gap-5 items-start">
    <img src="https://ui-avatars.com/api/?name={{ urlencode($post->author->name ?? 'Admin') }}&background=2755c8&color=fff&size=64"
         class="w-16 h-16 rounded-full" alt="{{ $post->author->name ?? 'Admin' }}" width="64" height="64" />
    <div>
      <h4 class="font-display font-semibold text-lg text-navy">{{ $post->author->name ?? 'Admin' }}</h4>
      <p class="text-sm text-muted mb-2">{{ $post->author->bio ?? 'Staff writer at Kusoma.' }}</p>
      <div class="flex gap-3 text-sm">
        <a href="{{ route('category.show', $post->category->slug ?? '#') }}" class="text-royal hover:underline">More in {{ $post->category->name ?? 'News' }}</a>
      </div>
    </div>
  </div>

  {{-- ═════════════════════════════════════════════════════════════════════ --}}
  {{-- COMMENTS SECTION                                                       --}}
  {{-- ═════════════════════════════════════════════════════════════════════ --}}
  <section class="mt-12" id="comments" aria-labelledby="comments-heading">
    <div class="flex items-center gap-4 mb-6">
      <h2 id="comments-heading" class="font-display font-semibold text-xl text-navy shrink-0">
        Discussion
        <span class="text-muted text-base font-normal ml-2">({{ $comments->count() ?? 0 }} comments)</span>
      </h2>
      <div class="flex-1 h-px bg-kborder"></div>
    </div>

    {{-- Flash success message --}}
    @if(session('success'))
      <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
      </div>
    @endif

    {{-- Comment form --}}
    <div class="bg-white border border-kborder rounded-xl p-5 mb-8">
      <h3 class="font-medium text-gray-800 mb-3">Join the conversation</h3>
      <form action="{{ route('post.comment', $post->slug) }}" method="POST" novalidate>
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
          <div>
            <input type="text" name="name" placeholder="Your name *"
                   value="{{ old('name') }}"
                   class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10 @error('name') border-red-400 @enderror"
                   required />
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <input type="email" name="email" placeholder="Email (optional)"
                   value="{{ old('email') }}"
                   class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10" />
          </div>
        </div>
        <textarea rows="3" name="comment" placeholder="Share your thoughts…"
                  class="w-full border border-kborder rounded-lg p-3 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10 font-sans mb-3 @error('comment') border-red-400 @enderror"
                  required>{{ old('comment') }}</textarea>
        @error('comment')<p class="text-xs text-red-500 -mt-2 mb-2">{{ $message }}</p>@enderror
        <div class="flex justify-end gap-2">
          <button type="reset" class="border border-kborder text-muted text-sm px-4 py-2 rounded-lg hover:bg-kbg transition">Cancel</button>
          <button type="submit" class="bg-royal hover:bg-navy text-white text-sm px-6 py-2 rounded-lg transition">Post comment</button>
        </div>
      </form>
    </div>

    {{-- Comments list --}}
    <div class="space-y-5">
      @forelse($comments ?? [] as $comment)
        @include('pages.news._comment', ['comment' => $comment])
      @empty
        <p class="text-sm text-muted text-center py-8">Be the first to comment.</p>
      @endforelse
    </div>
  </section>

  {{-- ── Related reading ──────────────────────────────────────────────── --}}
  @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
  <section class="mt-16" aria-labelledby="related-heading">
    <div class="flex items-center gap-4 mb-6">
      <h2 id="related-heading" class="font-display font-semibold text-xl text-navy shrink-0">Related reading</h2>
      <div class="flex-1 h-px bg-kborder"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($relatedPosts as $related)
      <div class="bg-white border border-kborder rounded-xl overflow-hidden">
        <div class="bg-{{ $related->category->css_suffix ?? 'kgreen' }} px-4 py-3 flex items-center">
          <h3 class="text-sm font-semibold text-white flex items-center gap-2">
            <i class="{{ $related->category->icon ?? 'fas fa-folder' }}"></i> {{ $related->category->name ?? 'News' }}
          </h3>
        </div>
        <div class="p-4">
          <a href="{{ route('post.show', $related->slug) }}"
             class="text-[15px] font-medium text-kgreen hover:underline block mb-2">
            {{ $related->title }}
          </a>
          <div class="flex gap-2 text-xs text-muted">
            <span class="text-royal">By {{ $related->author->name ?? 'Admin' }}</span>
            <span>•</span>
            <time datetime="{{ $related->published_at->toIso8601String() }}">
              {{ $related->published_at->format('M j') }}
            </time>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </section>
  @endif

</main>
@endsection