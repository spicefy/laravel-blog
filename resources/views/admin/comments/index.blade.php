{{-- resources/views/admin/comments/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Comments')

@section('topbar_actions')
  <div class="flex items-center gap-1 bg-kbg border border-kborder rounded-lg p-0.5 text-sm">
    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'all' => 'All'] as $val => $label)
    <a href="{{ route('admin.comments.index', ['filter' => $val]) }}"
       class="px-3 py-1 rounded-md transition-colors
              {{ (request('filter', 'pending') === $val) ? 'bg-white shadow-sm text-navy font-medium' : 'text-muted hover:text-navy' }}">
      {{ $label }}
    </a>
    @endforeach
  </div>
@endsection

@section('content')

{{-- Bulk approve all pending --}}
@if(request('filter', 'pending') === 'pending' && $comments->total() > 0)
<div class="mb-4 flex items-center justify-between bg-amber-50 border border-amber-200 rounded-xl px-5 py-3">
  <p class="text-sm text-amber-800">
    <i class="fas fa-clock mr-1.5"></i>
    <strong>{{ $comments->total() }}</strong> comment{{ $comments->total() !== 1 ? 's' : '' }} awaiting moderation.
  </p>
  <form action="{{ route('admin.comments.approveAll') }}" method="POST">
    @csrf @method('PATCH')
    <button class="text-sm text-green-700 font-medium hover:underline">
      Approve all →
    </button>
  </form>
</div>
@endif

<div class="bg-white border border-kborder rounded-xl overflow-hidden">

  <div class="divide-y divide-kborder">
    @forelse($comments as $comment)
    <div class="px-5 py-4 hover:bg-kbg transition-colors" id="comment-{{ $comment->id }}">
      <div class="flex items-start gap-4">

        <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->name) }}&background=6b7280&color=fff&size=36"
             class="w-9 h-9 rounded-full shrink-0 mt-0.5" alt="{{ $comment->name }}"
             width="36" height="36" />

        <div class="flex-1 min-w-0">

          {{-- Meta row --}}
          <div class="flex flex-wrap items-center gap-2 mb-1.5">
            <span class="font-medium text-sm text-gray-900">{{ $comment->name }}</span>
            @if($comment->email)
              <span class="text-xs text-muted">{{ $comment->email }}</span>
            @endif
            <span class="text-xs text-muted">·</span>
            <time class="text-xs text-muted">{{ $comment->created_at->format('M j, Y · g:ia') }}</time>

            {{-- Status badge --}}
            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full
              {{ $comment->approved ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
              {{ $comment->approved ? 'Approved' : 'Pending' }}
            </span>

            {{-- Reply badge --}}
            @if($comment->parent_id)
              <span class="text-[11px] bg-klight text-royal px-2 py-0.5 rounded-full">Reply</span>
            @endif
          </div>

          {{-- Post context --}}
          <div class="text-xs text-muted mb-2">
            On:
            <a href="{{ route('post.show', $comment->post->slug) }}" target="_blank"
               class="text-royal hover:underline">
              {{ $comment->post->title }}
            </a>
          </div>

          {{-- Comment body --}}
          <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ $comment->comment }}</p>

          {{-- Actions --}}
          <div class="flex items-center gap-4 text-xs">
            @if(!$comment->approved)
            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
              @csrf @method('PATCH')
              <button class="text-green-600 font-medium hover:underline flex items-center gap-1">
                <i class="fas fa-check text-[10px]"></i> Approve
              </button>
            </form>
            @endif

            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                  onsubmit="return confirm('Delete this comment permanently?')">
              @csrf @method('DELETE')
              <button class="text-red-500 hover:underline flex items-center gap-1">
                <i class="fas fa-trash text-[10px]"></i> Delete
              </button>
            </form>

            <span class="text-muted flex items-center gap-1">
              <i class="far fa-heart text-[10px]"></i> {{ $comment->likes }} likes
            </span>
          </div>

        </div>
      </div>
    </div>
    @empty
    <div class="px-5 py-12 text-center text-muted">
      <i class="fas fa-comments text-3xl mb-3 block opacity-20"></i>
      @if(request('filter', 'pending') === 'pending')
        No comments pending moderation. 🎉
      @else
        No comments found.
      @endif
    </div>
    @endforelse
  </div>

  @if($comments->hasPages())
  <div class="px-5 py-3.5 border-t border-kborder bg-kbg">
    {{ $comments->appends(request()->query())->links() }}
  </div>
  @endif

</div>
@endsection