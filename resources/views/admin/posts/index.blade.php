{{-- resources/views/admin/posts/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Posts')

@section('topbar_actions')
  {{-- Filter by status --}}
  <div class="flex items-center gap-1 bg-kbg border border-kborder rounded-lg p-0.5 text-sm">
    @foreach(['all' => 'All', 'published' => 'Published', 'draft' => 'Drafts'] as $val => $label)
    <a href="{{ route('admin.posts.index', ['status' => $val === 'all' ? null : $val]) }}"
       class="px-3 py-1 rounded-md transition-colors
              {{ (request('status', 'all') === $val) ? 'bg-white shadow-sm text-navy font-medium' : 'text-muted hover:text-navy' }}">
      {{ $label }}
    </a>
    @endforeach
  </div>
@endsection

@section('content')

<div class="bg-white border border-kborder rounded-xl overflow-hidden">

  {{-- Table header --}}
  <div class="grid grid-cols-12 gap-4 px-5 py-3 border-b border-kborder bg-kbg text-xs font-semibold text-muted uppercase tracking-wide">
    <div class="col-span-6">Title</div>
    <div class="col-span-2">Category</div>
    <div class="col-span-1 text-center">Status</div>
    <div class="col-span-1 text-center">Views</div>
    <div class="col-span-1 text-right">Published</div>
    <div class="col-span-1 text-right">Actions</div>
  </div>

  <div class="divide-y divide-kborder">
    @forelse($posts as $post)
    <div class="grid grid-cols-12 gap-4 px-5 py-3.5 items-center hover:bg-kbg transition-colors">

      {{-- Title --}}
      <div class="col-span-6 min-w-0">
        <a href="{{ route('admin.posts.edit', $post) }}"
           class="text-sm font-medium text-kgreen hover:underline block truncate">
          {{ $post->title }}
        </a>
        <div class="text-xs text-muted mt-0.5 flex items-center gap-1.5">
          <span>{{ $post->author->name }}</span>
          @if($post->tags->isNotEmpty())
            <span>·</span>
            <span class="truncate">{{ $post->tags->pluck('name')->join(', ') }}</span>
          @endif
        </div>
      </div>

      {{-- Category --}}
      <div class="col-span-2">
        <span class="text-xs font-medium text-{{ $post->category->css_suffix }}">
          {{ $post->category->name }}
        </span>
      </div>

      {{-- Status badge --}}
      <div class="col-span-1 flex justify-center">
        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full
          {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
          {{ ucfirst($post->status) }}
        </span>
      </div>

      {{-- Views --}}
      <div class="col-span-1 text-center text-sm text-muted">
        {{ number_format($post->view_count) }}
      </div>

      {{-- Date --}}
      <div class="col-span-1 text-right text-xs text-muted">
        @if($post->published_at)
          <time datetime="{{ $post->published_at->toIso8601String() }}">
            {{ $post->published_at->format('M j, Y') }}
          </time>
        @else
          <span class="italic">—</span>
        @endif
      </div>

      {{-- Actions --}}
      <div class="col-span-1 flex items-center justify-end gap-2">
        <a href="{{ route('post.show', $post->slug) }}" target="_blank"
           class="text-muted hover:text-royal transition-colors" title="View live">
          <i class="fas fa-arrow-up-right-from-square text-xs"></i>
        </a>
        <a href="{{ route('admin.posts.edit', $post) }}"
           class="text-muted hover:text-royal transition-colors" title="Edit">
          <i class="fas fa-pen text-xs"></i>
        </a>
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
              onsubmit="return confirm('Delete \'{{ addslashes($post->title) }}\'?')">
          @csrf @method('DELETE')
          <button class="text-muted hover:text-red-500 transition-colors" title="Delete">
            <i class="fas fa-trash text-xs"></i>
          </button>
        </form>
      </div>

    </div>
    @empty
    <div class="px-5 py-10 text-center text-muted">
      <i class="fas fa-newspaper text-2xl mb-2 block opacity-30"></i>
      No posts found.
      <a href="{{ route('admin.posts.create') }}" class="text-royal hover:underline ml-1">Create one →</a>
    </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($posts->hasPages())
  <div class="px-5 py-3.5 border-t border-kborder bg-kbg">
    {{ $posts->appends(request()->query())->links() }}
  </div>
  @endif

</div>
@endsection