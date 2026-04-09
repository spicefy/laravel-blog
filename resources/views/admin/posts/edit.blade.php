@extends('layouts.admin')

@section('title', 'Edit Post: ' . $post->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Post: {{ $post->title }}</h1>
        <div class="space-x-3">
            <a href="{{ route('admin.posts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Posts
            </a>
            @if($post->status === 'published')
                <a href="{{ url('/news/' . $post->slug) }}" target="_blank" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    View Post
                </a>
            @endif
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $post->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug (URL)
                    </label>
                    <input type="text" 
                           name="slug" 
                           id="slug" 
                           value="{{ old('slug', $post->slug) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Custom slug or leave empty to auto-generate from title</p>
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" 
                            id="category_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Excerpt --}}
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                        Excerpt (Short Description)
                    </label>
                    <textarea name="excerpt" 
                              id="excerpt" 
                              rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('excerpt', $post->excerpt) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">A brief summary of the post (max 255 characters)</p>
                </div>

                {{-- Content --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Content <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" 
                              id="content" 
                              rows="15" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                              required>{{ old('content', $post->content) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Supports HTML and Markdown</p>
                </div>

                {{-- Current Featured Image --}}
                @if($post->featured_image)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Current Featured Image
                    </label>
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="h-32 w-auto object-cover rounded border">
                    </div>
                </div>
                @endif

                {{-- New Featured Image --}}
                <div>
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $post->featured_image ? 'Replace Featured Image' : 'Featured Image' }}
                    </label>
                    <input type="file" 
                           name="featured_image" 
                           id="featured_image" 
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, GIF (Max 2MB)</p>
                </div>

                {{-- Tags Section --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tags
                    </label>
                    <select name="tags[]" 
                            multiple 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            size="5">
                        @foreach($allTags as $tag)
                            <option value="{{ $tag->id }}" 
                                {{ isset($post) && $post->tags->contains($tag->id) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple tags. Use Shift to select a range.</p>
                    @if($allTags->isEmpty())
                        <p class="text-xs text-red-500 mt-1">No tags available. <a href="{{ route('admin.tags.index') }}" class="text-blue-500 hover:underline">Create a tag first</a>.</p>
                    @endif
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                {{-- Post Meta Info --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Post Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="ml-2">{{ $post->created_at->format('F j, Y g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Last Modified:</span>
                            <span class="ml-2">{{ $post->updated_at->format('F j, Y g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Author:</span>
                            <span class="ml-2">{{ $post->author->name ?? 'Unknown' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Total Views:</span>
                            <span class="ml-2">{{ number_format($post->view_count) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('admin.posts.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Update Post
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate slug from title (only if slug is empty or user hasn't modified it)
    let slugManuallyEdited = false;
    const slugInput = document.getElementById('slug');
    const titleInput = document.getElementById('title');
    
    if (slugInput && titleInput) {
        slugInput.addEventListener('input', function() {
            slugManuallyEdited = true;
        });
        
        titleInput.addEventListener('keyup', function() {
            if (!slugManuallyEdited) {
                let title = this.value;
                let slug = title.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                slugInput.value = slug;
            }
        });
    }
</script>
@endpush
@endsection