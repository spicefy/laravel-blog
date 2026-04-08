{{-- resources/views/components/trending-sidebar.blade.php --}}
@props(['trendingPosts' => [], 'limit' => 5])

@if($trendingPosts && $trendingPosts->isNotEmpty())
<aside class="space-y-6">
    {{-- Trending Section --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 3.5L11.5 7H15L12 9L13.5 12.5L10 10.5L6.5 12.5L8 9L5 7H8.5L10 3.5Z"/>
            </svg>
            <h2 class="text-lg font-bold">Trending Now 🔥</h2>
        </div>

        <div class="space-y-4">
            @foreach($trendingPosts->take($limit) as $index => $trend)
                <a href="{{ route('post.show', $trend->slug) }}" 
                   class="flex gap-3 group hover:bg-gray-50 rounded-lg p-2 -mx-2 transition">
                    
                    {{-- Rank Badge --}}
                    <div class="flex-shrink-0 w-8 text-center">
                        <span class="text-2xl font-bold text-gray-300 group-hover:text-gray-400">
                            {{ $index + 1 }}
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1">
                        <p class="text-xs text-blue-600 font-semibold mb-1">
                            {{ $trend->category->name ?? 'News' }}
                        </p>
                        
                        <h3 class="text-sm font-medium leading-snug group-hover:text-blue-600 transition">
                            {{ Str::limit($trend->title, 70) }}
                        </h3>
                        
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                            <span>👁️ {{ number_format($trend->view_count ?? 0) }} views</span>
                            <span>💬 {{ $trend->comments_count ?? 0 }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Optional: Hot Topics / Tags --}}
    @if(isset($trendingTags) && $trendingTags->isNotEmpty())
    <div class="bg-white rounded-xl border p-5">
        <h3 class="font-semibold mb-3">🔥 Hot Topics</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($trendingTags as $tag)
                <a href="{{ route('tag.show', $tag->slug) }}" 
                   class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-full transition">
                    #{{ $tag->name }}
                    <span class="text-gray-500">({{ $tag->posts_count }})</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</aside>
@endif