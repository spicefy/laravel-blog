{{-- resources/views/pages/news/_comment.blade.php --}}
{{-- Renders a single comment and its nested replies recursively --}}

<div class="group relative bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300"
     id="comment-{{ $comment->id }}">

  {{-- Accent bar --}}
  <div class="absolute left-0 top-4 bottom-4 w-1 bg-emerald-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

  <div class="flex items-start gap-4">

    {{-- Avatar --}}
    <div class="relative shrink-0">
      <img
        src="https://ui-avatars.com/api/?name={{ urlencode($comment->name) }}&background=064e3b&color=fff&size=80&bold=true"
        class="w-11 h-11 rounded-full ring-2 ring-emerald-100"
        alt="{{ $comment->name }}"
      />
      <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 border-2 border-white rounded-full"></span>
    </div>

    <div class="flex-1 min-w-0">

      {{-- Header --}}
      <div class="flex items-center flex-wrap gap-2 mb-2">
        <span class="font-semibold text-slate-900 text-sm">
          {{ $comment->name }}
        </span>
        <span class="text-slate-300 text-xs">·</span>
        <time class="text-xs text-slate-400"
              datetime="{{ $comment->created_at->toIso8601String() }}">
          {{ $comment->created_at->diffForHumans() }}
        </time>
      </div>

      {{-- Comment body --}}
      <p class="text-sm text-slate-600 leading-relaxed mb-3">
    {{ $comment->comment }}
</p>

      {{-- Actions --}}
      <div class="flex items-center gap-4 text-xs">

        {{-- Like button --}}
        <button
          class="like-btn inline-flex items-center gap-1.5 text-slate-400 hover:text-rose-500 transition"
          data-url="{{ route('comment.like', $comment->id) }}"
        >
          <i class="far fa-heart text-xs"></i>
          <span class="like-count">{{ $comment->likes }}</span>
        </button>

        {{-- Reply toggle --}}
        <button
          class="reply-toggle inline-flex items-center gap-1.5 text-slate-400 hover:text-emerald-600 transition"
          data-target="reply-{{ $comment->id }}"
          type="button"
        >
          <i class="far fa-comment-dots text-xs"></i>
          Reply
        </button>

      </div>

      {{-- Reply form --}}
      <div id="reply-{{ $comment->id }}" class="hidden mt-4">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">

          <form action="{{ route('post.comment', request()->route('slug')) }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
              <input type="text" name="name" placeholder="Your name *" required
                     class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-full">

              <input type="email" name="email" placeholder="Email (optional)"
                     class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-full">
            </div>

            {{-- IMPORTANT FIX: use "comment" not "content" --}}
            <<textarea
    name="comment"
    rows="2"
    placeholder="Write a reply…"
    required
    class="w-full border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition resize-none mb-3"
></textarea>

            <div class="flex gap-2">
              <button type="submit"
                      class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-5 py-2 rounded-lg">
                Post reply
              </button>

              <button type="button"
                      class="reply-toggle text-xs text-slate-500 hover:text-slate-700 px-3 py-2"
                      data-target="reply-{{ $comment->id }}">
                Cancel
              </button>
            </div>

          </form>

        </div>
      </div>

      {{-- Nested replies --}}
      @if($comment->replies->isNotEmpty())
        <div class="mt-5 pl-5 border-l-2 border-slate-100 space-y-4">
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
document.addEventListener('click', async (e) => {

  /* ---------------- Reply toggle ---------------- */
  const replyBtn = e.target.closest('.reply-toggle');
  if (replyBtn) {
    const target = document.getElementById(replyBtn.dataset.target);
    if (target) target.classList.toggle('hidden');
    return;
  }

  /* ---------------- Like button ---------------- */
  const likeBtn = e.target.closest('.like-btn');
  if (!likeBtn) return;

  const url = likeBtn.dataset.url;
  const count = likeBtn.querySelector('.like-count');

  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
    });

    const data = await res.json();
    count.textContent = data.likes;

    if (data.message === 'liked') {
      likeBtn.classList.remove('text-slate-400');
      likeBtn.classList.add('text-rose-500');
      likeBtn.querySelector('i').classList.replace('far', 'fas');
    }

  } catch (err) {
    console.error('Like failed', err);
  }
});
</script>
@endpush
@endonce