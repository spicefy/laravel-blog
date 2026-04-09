@extends('layouts.admin')
@section('title', 'Edit Tag')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="bg-white border border-kborder rounded-xl overflow-hidden">
    <div class="px-5 py-3.5 border-b border-kborder bg-kbg flex justify-between items-center">
      <h2 class="text-sm font-semibold text-gray-800">Edit Tag</h2>
      <a href="{{ route('admin.tags.index') }}" class="text-muted hover:text-gray-700 text-sm">
        <i class="fas fa-times"></i> Cancel
      </a>
    </div>
    
    @if(session('success'))
    <div class="mx-5 mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
      {{ session('success') }}
    </div>
    @endif
    
    <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="p-5 space-y-4">
      @csrf
      @method('PUT')
      
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Tag Name <span class="text-red-500">*</span></label>
        <input type="text" 
               name="name" 
               value="{{ old('name', $tag->name) }}"
               required
               class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10 @error('name') border-red-500 @enderror"
               placeholder="e.g. Laravel, PHP, JavaScript" />
        @error('name')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>
      
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Slug (Auto-generated)</label>
        <input type="text" 
               value="{{ $tag->slug }}"
               disabled
               class="w-full bg-gray-50 border border-kborder rounded-lg px-3 py-2 text-sm text-muted"
               readonly />
        <p class="text-xs text-muted mt-1">The slug is automatically generated from the name.</p>
      </div>
      
      <div class="flex gap-3 pt-3">
        <button type="submit"
                class="flex-1 bg-royal hover:bg-navy text-white text-sm font-medium py-2 rounded-lg transition-colors">
          <i class="fas fa-save"></i> Update Tag
        </button>
        <a href="{{ route('admin.tags.index') }}"
           class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium py-2 rounded-lg transition-colors text-center">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>
@endsection