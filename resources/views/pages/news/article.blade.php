{{-- resources/views/pages/news/article.blade.php --}}
@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    $metaDescription = $post->meta_description 
        ?? Str::limit(strip_tags($post->excerpt ?? $post->content ?? ''), 155);

    $authorName = $post->author->name ?? 'Admin';
    $authorBio = $post->author?->bio ?? 'Staff writer';
    $categoryName = $post->category->name ?? 'News';
@endphp

@section('title', $post->title)
@section('meta_description', $metaDescription)

@section('content')
<div class="max-w-7xl mx-auto px-5 py-10">
    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- Main Content Column --}}
        <article class="lg:w-2/3">
            {{-- Category --}}
            <p class="text-sm text-blue-600 font-semibold mb-2 uppercase tracking-wide">
                {{ $categoryName }}
            </p>

            {{-- Title --}}
            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-4">
                {{ $post->title }}
            </h1>

            {{-- Meta --}}
            <div class="flex items-center gap-3 text-sm text-red-500 mb-8">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($authorName) }}"
                     class="w-10 h-10 rounded-full">

                <div>
                    <p class="font-medium text-gray-800">{{ $authorName }}</p>
                    <p class="text-xs">
                        {{ $post->published_at?->format('M j, Y') }} · 
                        {{ $post->reading_time ?? 3 }} min read
                       👁 {{ $post->view_count ?? 0 }} views 
                       
                    </p>
                </div>
            </div>

            {{-- Featured Image --}}
            @if($post->featured_image)
                <div class="mb-10">
                    <img src="{{ asset($post->featured_image) }}"
                         class="w-full rounded-xl object-cover max-h-[500px]">
                </div>
            @endif

            {{-- Article Content --}}
            <div class="prose prose-lg max-w-none prose-headings:font-semibold prose-p:leading-relaxed">
                {!! $post->content !!}
            </div>

            {{-- Tags --}}
            @if($post->tags && $post->tags->isNotEmpty())
                <div class="mt-10 pt-6 border-t">
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                            <a href="{{ route('tag.show', $tag->slug) }}"
                               class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-full transition">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Author Box --}}
            <div class="mt-12 border rounded-xl p-6 flex gap-4 bg-gray-50">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($authorName) }}"
                     class="w-14 h-14 rounded-full">

                <div>
                    <h4 class="font-semibold text-lg">{{ $authorName }}</h4>
                    <p class="text-sm text-gray-600">{{ $authorBio }}</p>
                </div>
            </div>

            {{-- Related Posts --}}
            @if($relatedPosts && $relatedPosts->isNotEmpty())
                <section class="mt-16">
                    <h2 class="text-xl font-semibold mb-6">Related stories</h2>
                    <div class="grid md:grid-cols-2 gap-5">
                        @foreach($relatedPosts as $related)
                            <a href="{{ route('post.show', $related->slug) }}"
                               class="group block border rounded-xl overflow-hidden hover:shadow-md transition">
                                <div class="h-40 bg-gray-100 overflow-hidden">
                                    @if($related->featured_image)
                                        <img src="{{ asset($related->featured_image) }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                            No image
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <p class="text-xs text-blue-600 font-semibold mb-1">
                                        {{ $related->category->name ?? 'News' }}
                                    </p>
                                    <h3 class="font-semibold text-sm leading-snug group-hover:underline">
                                        {{ Str::limit($related->title, 80) }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-2">
                                        {{ $related->published_at?->format('M j, Y') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Comments --}}
            <section class="mt-16">
                <h2 class="text-xl font-semibold mb-6">
                    Discussion ({{ $comments?->count() ?? 0 }})
                </h2>

                <form action="{{ route('post.comment', $post->slug) }}" method="POST" class="mb-8">
                    @csrf
                    <input type="text" name="name" placeholder="Your name"
                           class="w-full border rounded-lg p-3 mb-3 text-sm" required>
                    <textarea name="comment" rows="3"
                              placeholder="Write your comment..."
                              class="w-full border rounded-lg p-3 mb-3 text-sm" required></textarea>
                    <button class="bg-black text-white px-5 py-2 rounded-lg text-sm hover:opacity-90">
                        Post Comment
                    </button>
                </form>

                <div class="space-y-6">
                    @forelse($comments ?? [] as $comment)
                        <div class="border-b pb-4">
                            <p class="font-medium text-sm">{{ $comment->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $comment->comment }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">No comments yet.</p>
                    @endforelse
                </div>
            </section>
        </article>

        {{-- Sidebar Column --}}
       
<aside class="lg:w-1/3">
    <x-trending-sidebar 
        :trendingPosts="$trendingPosts" 
        :trendingTags="$trendingTags ?? null"
        limit="5" 
    />

    {{-- Optional: Add a simple "Follow Us" section instead --}}
    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 mt-6">
        <h3 class="font-semibold mb-2">📱 Follow Us</h3>
        <p class="text-sm text-gray-600 mb-3">Stay updated with latest news</p>
        <div class="flex gap-2">
            <a href="#" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm text-center hover:bg-blue-700">
                Twitter
            </a>
            <a href="#" class="flex-1 bg-red-600 text-white px-3 py-2 rounded-lg text-sm text-center hover:bg-red-700">
                YouTube
            </a>
        </div>
    </div>
</aside>
    </div>
</div>
@endsection