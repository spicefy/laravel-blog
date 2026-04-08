{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- ── Categories list (left 2/3) ──────────────────────────────────── --}}
  <div class="lg:col-span-2">
    <div class="bg-white border border-kborder rounded-xl overflow-hidden">
      <div class="px-5 py-3.5 border-b border-kborder bg-kbg grid grid-cols-12 text-xs font-semibold text-muted uppercase tracking-wide">
        <div class="col-span-4">Name</div>
        <div class="col-span-2">Slug</div>
        <div class="col-span-2">Colour</div>
        <div class="col-span-2 text-center">Posts</div>
        <div class="col-span-2 text-right">Actions</div>
      </div>

      <div class="divide-y divide-kborder">
        @forelse($categories as $category)
        <div class="px-5 py-3.5 grid grid-cols-12 items-center gap-2 hover:bg-kbg transition-colors">

          <div class="col-span-4 flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                  style="background-color: {{ $category->color }}20">
              <i class="{{ $category->icon }} text-xs" style="color: {{ $category->color }}"></i>
            </span>
            <span class="font-medium text-sm text-gray-900">{{ $category->name }}</span>
          </div>

          <div class="col-span-2 text-xs text-muted font-mono">/{{ $category->slug }}</div>

          <div class="col-span-2 flex items-center gap-1.5">
            <span class="w-4 h-4 rounded-full shrink-0"
                  style="background-color: {{ $category->color }}"></span>
            <span class="text-xs text-muted font-mono">{{ $category->color }}</span>
          </div>

          <div class="col-span-2 text-center">
            <span class="text-sm font-medium text-gray-800">{{ $category->posts_count }}</span>
          </div>

          <div class="col-span-2 flex items-center justify-end gap-2">
            <a href="{{ route('category.show', $category->slug) }}" target="_blank"
               class="text-muted hover:text-royal text-xs" title="View archive">
              <i class="fas fa-arrow-up-right-from-square"></i>
            </a>
            <a href="{{ route('admin.categories.edit', $category) }}"
               class="text-muted hover:text-royal text-xs" title="Edit">
              <i class="fas fa-pen"></i>
            </a>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                  onsubmit="return confirm('Delete category \'{{ $category->name }}\'? All posts in it will be unlinked.')">
              @csrf @method('DELETE')
              <button class="text-muted hover:text-red-500 text-xs" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </div>

        </div>
        @empty
        <div class="px-5 py-8 text-center text-sm text-muted italic">No categories yet.</div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- ── Quick-add / edit form (right 1/3) ───────────────────────────── --}}
  <div>
    <div class="bg-white border border-kborder rounded-xl overflow-hidden">
      <div class="px-5 py-3.5 border-b border-kborder bg-kbg">
        <h2 class="text-sm font-semibold text-gray-800">
          {{ isset($editing) ? 'Edit category' : 'Add category' }}
        </h2>
      </div>

      <form action="{{ isset($editing) ? route('admin.categories.update', $editing) : route('admin.categories.store') }}"
            method="POST" class="p-5 space-y-4">
        @csrf
        @if(isset($editing)) @method('PUT') @endif

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
          <input type="text" name="name" required
                 value="{{ old('name', $editing->name ?? '') }}"
                 class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10"
                 placeholder="e.g. Education" />
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Slug</label>
          <input type="text" name="slug"
                 value="{{ old('slug', $editing->slug ?? '') }}"
                 class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10 font-mono"
                 placeholder="auto-generated" />
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Colour (hex)</label>
          <div class="flex gap-2">
            <input type="color" name="color"
                   value="{{ old('color', $editing->color ?? '#2755c8') }}"
                   class="w-10 h-9 border border-kborder rounded-lg cursor-pointer p-0.5" />
            <input type="text" name="color_text"
                   value="{{ old('color', $editing->color ?? '#2755c8') }}"
                   class="flex-1 border border-kborder rounded-lg px-3 py-2 text-sm font-mono outline-none focus:border-royal"
                   placeholder="#2755c8" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Icon (Font Awesome class)</label>
          <input type="text" name="icon"
                 value="{{ old('icon', $editing->icon ?? '') }}"
                 class="w-full border border-kborder rounded-lg px-3 py-2 text-sm font-mono outline-none focus:border-royal focus:ring-2 focus:ring-royal/10"
                 placeholder="fas fa-graduation-cap" />
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
          <textarea name="description" rows="2"
                    class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10"
                    placeholder="Short description for the archive page">{{ old('description', $editing->description ?? '') }}</textarea>
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Meta Description</label>
          <textarea name="meta_description" rows="2" maxlength="160"
                    class="w-full border border-kborder rounded-lg px-3 py-2 text-sm outline-none focus:border-royal focus:ring-2 focus:ring-royal/10"
                    placeholder="160 chars max — shown in Google">{{ old('meta_description', $editing->meta_description ?? '') }}</textarea>
        </div>

        <div class="flex gap-2 pt-1">
          <button type="submit"
                  class="flex-1 bg-royal hover:bg-navy text-white text-sm font-medium py-2 rounded-lg transition-colors">
            {{ isset($editing) ? 'Update' : 'Add category' }}
          </button>
          @if(isset($editing))
          <a href="{{ route('admin.categories.index') }}"
             class="px-4 py-2 border border-kborder text-sm text-muted rounded-lg hover:bg-kbg transition-colors">
            Cancel
          </a>
          @endif
        </div>
      </form>
    </div>
  </div>

</div>
@endsection