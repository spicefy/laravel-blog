{{-- resources/views/admin/tags/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Tags')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- Tags list --}}
  <div class="lg:col-span-2 bg-white border border-kborder rounded-xl overflow-hidden">
    <div class="px-5 py-3.5 border-b border-kborder bg-kbg grid grid-cols-12 text-xs font-semibold text-muted uppercase tracking-wide">
      <div class="col-span-5">Name</div>
      <div class="col-span-4">Slug</div>
      <div class="col-span-2 text-center">Posts</div>
      <div class="col-span-1 text-right">Actions</div>
    </div>
    <div class="divide-y divide-kborder">
      @forelse($tags as $tag)
      <div class="px-5 py-3 grid grid-cols-12 items-center gap-2 hover:bg-kbg transition-colors">
        <div class="col-span-5 text-sm font-medium text-gray-800">{{ $tag->name }}</div>
        <div class="col-span-4 text-xs text-muted font-mono">{{ $tag->slug }}</div>
        <div class="col-span-2 text-center text-sm text-muted">{{ $tag->posts_count }}</div>
        <div class="col-span-1 flex justify-end gap-2">
          {{-- Edit button --}}
          <a href="{{ route('admin.tags.edit', $tag) }}" 
             class="text-muted hover:text-royal transition-colors text-xs"
             title="Edit tag">
            <i class="fas fa-edit"></i>
          </a>
          
          {{-- Delete button --}}
          <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                onsubmit="return confirm('Delete tag \'{{ $tag->name }}\'?')"
                class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-muted hover:text-red-500 transition-colors text-xs" title="Delete tag">
              <i class="fas fa-trash"></i>
            </button>
          </form>
        </div>
      </div>
      @empty
      <div class="px-5 py-8 text-center text-sm text-muted italic">No tags yet.</div>
      @endforelse
    </div>
    
    {{-- Pagination --}}
    @if($tags->hasPages())
    <div class="px-5 py-3 border-t border-kborder bg-kbg">
      {{ $tags->links() }}
    </div>
    @endif
  </div>

  {{-- Quick-add form --}}
  <div class="bg-white border border-kborder rounded-xl overflow-hidden h-fit">
    <div class="px-5 py-3.5 border-b border-kborder bg-kbg">
      <h2 class="text-sm font-semibold text-gray-800">Add New Tag</h2>
    </div>
    
    {{-- Display success/error messages --}}
    @if(session('success'))
    <div class="mx-5 mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
      {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="mx-5 mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
      {{ session('error') }}
    </div>
    @endif
    
    <form action="{{ route('admin.tags.store') }}" method="POST" class="p-5 space-y-3">
      @csrf
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Tag Name <span class="text-red-500">*</span></label>
        <input type="text" 
               name="name" 
               value="{{ old('name') }}"
               required
               class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10 @error('name') border-red-500 @enderror"
               placeholder="e.g. Laravel, PHP, JavaScript" />
        @error('name')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
        <p class="text-xs text-muted mt-1">Slug will be automatically generated from the name.</p>
      </div>
      
      <button type="submit"
              class="w-full bg-royal hover:bg-navy text-white text-sm font-medium py-2 rounded-lg transition-colors">
        <i class="fas fa-plus-circle"></i> Add Tag
      </button>
    </form>
  </div>

</div>
@endsection

{{-- Optional: Add custom CSS for better pagination styling --}}
@push('styles')
<style>
  /* Custom pagination styling if needed */
  .pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
  }
  .pagination .page-item .page-link {
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    color: #4B5563;
    background-color: white;
    border: 1px solid #E5E7EB;
  }
  .pagination .page-item.active .page-link {
    background-color: #7C3AED;
    border-color: #7C3AED;
    color: white;
  }
</style>
@endpush