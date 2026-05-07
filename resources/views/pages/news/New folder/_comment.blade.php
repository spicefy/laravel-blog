{{-- resources/views/pages/news/_comment.blade.php --}}
{{-- Renders a single comment and its nested replies recursively --}}
<div class="bg-white border border-kborder rounded-xl p-5" id="comment-{{ $comment->id }}">
  <div class="flex items-start gap-3">
    <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->name) }}&background=1a7a45&color=fff&size=40"
         class="w-10 h-10 rounded-full shrink-0" alt="{{ $comment->name }}" width="40" height="40" />
    <div class="flex-1 min-w-0">
      <div class="flex items-center flex-wrap gap-2 mb-1">
        <span class="font-medium text-gray-900">{{ $comment->name }}</span>
        <time class="text-xs text-muted" datetime="{{ $comment->created_at->toIso8601String() }}">
          · {{ $comment->created_at->diffForHumans() }}
        </time>
      </div>
      <p class="text-sm text-gray-700 mb-2">{{ $comment->comment }}</p>
      <div class="flex items-center gap-3 text-xs">
        <button class="text-royal hover:underline flex items-center gap-1">
          <i class="far fa-heart"></i> <span>{{ $comment->likes }}</span>
        </button>
        <button class="text-muted hover:text-royal reply-toggle" data-target="reply-{{ $comment->id }}">
          Reply
        </button>
      </div>

      {{-- Reply form (hidden by default, toggled via JS) --}}
      <div id="reply-{{ $comment->id }}" class="hidden mt-3">
        <form action="{{ route('post.comment', request()->route('slug')) }}" method="POST">
          @csrf
          <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
          <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
            <input type="text" name="name" placeholder="Your name *" required
                   class="border border-kborder rounded-lg px-3 py-1.5 text-sm outline-none focus:border-royal" />
            <input type="email" name="email" placeholder="Email (optional)"
                   class="border border-kborder rounded-lg px-3 py-1.5 text-sm outline-none focus:border-royal" />
          </div>
          <textarea name="comment" rows="2" placeholder="Write a reply…" required
                    class="w-full border border-kborder rounded-lg p-2 text-sm outline-none focus:border-royal mb-2"></textarea>
          <div class="flex gap-2">
            <button type="submit" class="bg-royal text-white text-xs px-4 py-1.5 rounded-lg hover:bg-navy transition">Post reply</button>
            <button type="button" class="text-xs text-muted hover:text-gray-700 reply-toggle" data-target="reply-{{ $comment->id }}">Cancel</button>
          </div>
        </form>
      </div>

      {{-- Nested replies --}}
      @if($comment->replies->isNotEmpty())
        <div class="mt-4 pl-4 border-l-2 border-kborder space-y-4">
          @foreach($comment->replies as $reply)
            @include('pages.news._comment', ['comment' => $reply])
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>

@once
@push('scripts')
<script>
  document.querySelectorAll('.reply-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.getElementById(btn.dataset.target);
      if (target) target.classList.toggle('hidden');
    });
  });
</script>
@endpush
@endonce
