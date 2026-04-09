@extends('layouts.admin')

@section('title', $post->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
            <h1 class="text-2xl font-bold">{{ $post->title }}</h1>
            <div class="space-x-2">
                <a href="{{ route('admin.posts.edit', $post) }}" 
                   class="text-blue-600 hover:text-blue-800">Edit</a>
                <form action="{{ route('admin.posts.destroy', $post) }}" 
                      method="POST" 
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="text-red-600 hover:text-red-800"
                            onclick="return confirm('Delete this post?')">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        
        <div class="text-gray-600 mb-4">
            <span>By {{ $post->author->name ?? 'Unknown' }}</span>
            <span class="mx-2">•</span>
            <span>{{ $post->created_at->format('M d, Y') }}</span>
            <span class="mx-2">•</span>
            <span>{{ number_format($post->view_count) }} views</span>
        </div>
        
        @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                 class="w-full h-64 object-cover rounded mb-4">
        @endif
        
        <div class="prose max-w-none">
            {!! nl2br(e($post->content)) !!}
        </div>
    </div>
</div>
@endsection