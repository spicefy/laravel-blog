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
      <div class="col-span-1 text-right">Del</div>
    </div>
    <div class="divide-y divide-kborder">
      @forelse($tags as $tag)
      <div class="px-5 py-3 grid grid-cols-12 items-center gap-2 hover:bg-kbg transition-colors">
        <div class="col-span-5 text-sm font-medium text-gray-800">{{ $tag->name }}</div>
        <div class="col-span-4 text-xs text-muted font-mono">{{ $tag->slug }}</div>
        <div class="col-span-2 text-center text-sm text-muted">{{ $tag->posts_count }}</div>
        <div class="col-span-1 flex justify-end">
          <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                onsubmit="return confirm('Delete tag \'{{ $tag->name }}\'?')">
            @csrf @method('DELETE')
            <button class="text-muted hover:text-red-500 transition-colors text-xs">
              <i class="fas fa-trash"></i>
            </button>
          </form>
        </div>
      </div>
      @empty
      <div class="px-5 py-8 text-center text-sm text-muted italic">No tags yet.</div>
      @endforelse
    </div>
    @if($tags->hasPages())
    <div class="px-5 py-3 border-t border-kborder bg-kbg">{{ $tags->links() }}</div>
    @endif
  </div>

  {{-- Quick-add form --}}
  <div class="bg-white border border-kborder rounded-xl overflow-hidden h-fit">
    <div class="px-5 py-3.5 border-b border-kborder bg-kbg">
      <h2 class="text-sm font-semibold text-gray-800">Add tag</h2>
    </div>
    <form action="{{ route('admin.tags.store') }}" method="POST" class="p-5 space-y-3">
      @csrf
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Tag name *</label>
        <input type="text" name="name" required
               class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10"
               placeholder="e.g. KCSE" />
      </div>
      <button type="submit"
              class="w-full bg-royal hover:bg-navy text-white text-sm font-medium py-2 rounded-lg transition-colors">
        Add tag
      </button>
    </form>
  </div>

</div>
@endsection