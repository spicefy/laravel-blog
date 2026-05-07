{{-- resources/views/pages/news/article.blade.php --}}
@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    $metaDescription = $post->meta_description
        ?? Str::limit(strip_tags($post->excerpt ?? $post->content ?? ''), 155);

    $authorName   = $post->author->name ?? 'Admin';
    $authorBio    = $post->author?->bio ?? 'Staff writer';
    $categoryName = $post->category->name ?? 'News';
@endphp

@section('title', $post->title)
@section('meta_description', $metaDescription)

@section('content')

{{-- ── Hero banner ──────────────────────────────────────────────────── --}}
@if($post->featured_image)
<div class="relative w-full h-[55vh] min-h-80 overflow-hidden bg-slate-900">
  <img
    src="{{ asset($post->featured_image) }}"
    class="absolute inset-0 w-full h-full object-cover opacity-60"
    alt="{{ $post->title }}"
  />
  {{-- Gradient overlay --}}
  <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>

  {{-- Hero text --}}
  <div class="absolute bottom-0 left-0 right-0 max-w-4xl mx-auto px-6 pb-10">
    <a
      href="{{ route('category.show', $post->category->slug ?? '#') }}"
      class="inline-flex items-center gap-1.5 text-emerald-400 text-xs font-bold uppercase tracking-widest mb-3 hover:text-emerald-300 transition"
    >
      <span class="w-4 h-px bg-emerald-400"></span>
      {{ $categoryName }}
    </a>
    <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight tracking-tight mb-4 drop-shadow-lg">
      {{ $post->title }}
    </h1>
    <div class="flex items-center gap-3 text-sm text-slate-300">
      <img
        src="https://ui-avatars.com/api/?name={{ urlencode($authorName) }}&background=064e3b&color=fff&size=64&bold=true"
        class="w-9 h-9 rounded-full ring-2 ring-emerald-500/50"
        alt="{{ $authorName }}"
      />
      <div>
        <span class="font-semibold text-white">{{ $authorName }}</span>
        <span class="text-slate-400 mx-1.5">·</span>
        <span>{{ $post->published_at?->format('M j, Y') }}</span>
        <span class="text-slate-400 mx-1.5">·</span>
        <span>{{ $post->reading_time ?? 3 }} min read</span>
        <span class="text-slate-400 mx-1.5">·</span>
        <span>👁 {{ number_format($post->view_count ?? 0) }}</span>
      </div>
    </div>
  </div>
</div>
@endif

{{-- ── Page body ─────────────────────────────────────────────────────── --}}
<div class="max-w-7xl mx-auto px-5 py-12">
  <div class="flex flex-col lg:flex-row gap-12">

    {{-- ── Article column ─────────────────────────────────────────── --}}
    <article class="lg:w-2/3 min-w-0">

      {{-- Show title + meta above fold when no hero image --}}
      @if(!$post->featured_image)
        <a
          href="{{ route('category.show', $post->category->slug ?? '#') }}"
          class="inline-flex items-center gap-1.5 text-emerald-600 text-xs font-bold uppercase tracking-widest mb-4 hover:text-emerald-700 transition"
        >
          <span class="w-4 h-px bg-emerald-600"></span>
          {{ $categoryName }}
        </a>

        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight tracking-tight text-slate-900 mb-5">
          {{ $post->title }}
        </h1>

        <div class="flex items-center gap-3 text-sm text-slate-500 mb-8 pb-8 border-b border-slate-100">
          <img
            src="https://ui-avatars.com/api/?name={{ urlencode($authorName) }}&background=064e3b&color=fff&size=64&bold=true"
            class="w-9 h-9 rounded-full"
            alt="{{ $authorName }}"
          />
          <div>
            <span class="font-semibold text-slate-800">{{ $authorName }}</span>
            <span class="text-slate-300 mx-1.5">·</span>
            <span>{{ $post->published_at?->format('M j, Y') }}</span>
            <span class="text-slate-300 mx-1.5">·</span>
            <span>{{ $post->reading_time ?? 3 }} min read</span>
            <span class="text-slate-300 mx-1.5">·</span>
            <span>👁 {{ number_format($post->view_count ?? 0) }}</span>
          </div>
        </div>
      @else
        {{-- Divider after hero --}}
        <div class="border-b border-slate-100 mb-10"></div>
      @endif

      {{-- ── Article body ──────────────────────────────────────────── --}}
      <div class="prose prose-lg max-w-none
                  prose-headings:font-bold prose-headings:tracking-tight prose-headings:text-slate-900
                  prose-p:text-slate-600 prose-p:leading-relaxed
                  prose-a:text-emerald-600 prose-a:no-underline hover:prose-a:underline
                  prose-blockquote:border-emerald-500 prose-blockquote:bg-emerald-50 prose-blockquote:rounded-lg prose-blockquote:not-italic
                  prose-img:rounded-xl prose-img:shadow-md
                  prose-code:bg-slate-100 prose-code:text-emerald-700 prose-code:rounded prose-code:px-1">
        {!! $post->content !!}
      </div>

      {{-- ── Tags ────────────────────────────────────────────────────── --}}
      @if($post->tags && $post->tags->isNotEmpty())
        <div class="mt-10 pt-8 border-t border-slate-100">
          <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Tagged in</p>
          <div class="flex flex-wrap gap-2">
            @foreach($post->tags as $tag)
              <a
                href="{{ route('tag.show', $tag->slug) }}"
                class="text-xs font-medium bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 px-3 py-1.5 rounded-full border border-slate-200 hover:border-emerald-200 transition"
              >
                #{{ $tag->name }}
              </a>
            @endforeach
          </div>
        </div>
      @endif

      {{-- ── Author box ──────────────────────────────────────────────── --}}
      <div class="mt-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 p-7 flex gap-5 items-start">
        <img
          src="https://ui-avatars.com/api/?name={{ urlencode($authorName) }}&background=064e3b&color=fff&size=96&bold=true"
          class="w-16 h-16 rounded-full ring-4 ring-emerald-100 shrink-0"
          alt="{{ $authorName }}"
        />
        <div>
          <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-1">Written by</p>
          <h4 class="font-extrabold text-lg text-slate-900 mb-1">{{ $authorName }}</h4>
          <p class="text-sm text-slate-500 leading-relaxed">{{ $authorBio }}</p>
        </div>
      </div>

      {{-- ── Related posts ───────────────────────────────────────────── --}}
      @if($relatedPosts && $relatedPosts->isNotEmpty())
        <section class="mt-16">
          <div class="flex items-center gap-4 mb-7">
            <h2 class="font-extrabold text-xl text-slate-900 shrink-0">Related stories</h2>
            <div class="flex-1 h-px bg-slate-100"></div>
          </div>

          <div class="grid md:grid-cols-2 gap-5">
            @foreach($relatedPosts as $related)
              <a
                href="{{ route('post.show', $related->slug) }}"
                class="group block bg-white border border-slate-100 rounded-2xl overflow-hidden hover:shadow-lg transition-shadow duration-300"
              >
                <div class="h-44 bg-slate-100 overflow-hidden relative">
                  @if($related->featured_image)
                    <img
                      src="{{ asset($related->featured_image) }}"
                      class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                      alt="{{ $related->title }}"
                    />
                  @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                      <i class="fas fa-newspaper text-slate-300 text-3xl"></i>
                    </div>
                  @endif
                  {{-- Category badge --}}
                  <span class="absolute top-3 left-3 bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
                    {{ $related->category->name ?? 'News' }}
                  </span>
                </div>

                <div class="p-5">
                  <h3 class="font-bold text-sm text-slate-900 leading-snug group-hover:text-emerald-700 transition mb-2">
                    {{ Str::limit($related->title, 80) }}
                  </h3>
                  <p class="text-xs text-slate-400">
                    {{ $related->published_at?->format('M j, Y') }}
                  </p>
                </div>
              </a>
            @endforeach
          </div>
        </section>
      @endif

      {{-- ── Comments ─────────────────────────────────────────────────── --}}
      <section class="mt-16">
        <div class="flex items-center gap-4 mb-8">
          <h2 class="font-extrabold text-xl text-slate-900 shrink-0">
            Discussion
            <span class="ml-1.5 text-base font-semibold text-slate-400">({{ $comments?->count() ?? 0 }})</span>
          </h2>
          <div class="flex-1 h-px bg-slate-100"></div>
        </div>

        {{-- Comment form --}}
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-10">
          <p class="text-sm font-semibold text-slate-700 mb-4">Leave a comment</p>
          <form action="{{ route('post.comment', $post->slug) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
              <input
                type="text" name="name" placeholder="Your name *" required
                class="border border-slate-200 bg-white rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition"
              />
              <input
                type="email" name="email" placeholder="Email (optional)"
                class="border border-slate-200 bg-white rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition"
              />
            </div>
            <textarea
              name="comment" rows="4" placeholder="Share your thoughts…" required
              class="w-full border border-slate-200 bg-white rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition resize-none mb-4"
            ></textarea>
            <button
              type="submit"
              class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition-colors duration-200"
            >
              Post Comment
            </button>
          </form>
        </div>

        {{-- Comments list --}}
        <div class="space-y-5">
          @forelse($comments ?? [] as $comment)
            @include('pages.news._comment', ['comment' => $comment])
          @empty
            <div class="text-center py-16 text-slate-400">
              <i class="far fa-comment-dots text-4xl mb-3 block"></i>
              <p class="text-sm font-medium">No comments yet. Be the first!</p>
            </div>
          @endforelse
        </div>
      </section>

    </article>

    {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
    <aside class="lg:w-1/3 space-y-6">

      {{-- Trending sidebar component --}}
      <x-trending-sidebar
        :trendingPosts="$trendingPosts"
        :trendingTags="$trendingTags ?? null"
        limit="5"
      />

      {{-- Follow us card --}}
      <div class="bg-slate-900 rounded-2xl p-6 text-white">
        <p class="text-xs font-bold uppercase tracking-widest text-emerald-400 mb-2">Stay Connected</p>
        <h3 class="font-extrabold text-lg mb-1">Follow Us</h3>
        <p class="text-sm text-slate-400 mb-5 leading-relaxed">Get the latest education & tech news delivered to your feed.</p>
        <div class="grid grid-cols-2 gap-3">
          <a
            href="#"
            class="flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-400 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors duration-200"
          >
            <i class="fab fa-twitter text-xs"></i> Twitter
          </a>
          <a
            href="#"
            class="flex items-center justify-center gap-2 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors duration-200"
          >
            <i class="fab fa-youtube text-xs"></i> YouTube
          </a>
          <a
            href="#"
            class="flex items-center justify-center gap-2 col-span-2 bg-blue-700 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors duration-200"
          >
            <i class="fab fa-facebook-f text-xs"></i> Facebook
          </a>
        </div>
      </div>

    </aside>
  </div>
</div>
@endsection
