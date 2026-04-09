{{-- resources/views/admin/comments/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Comments')

@section('content')
<div class="space-y-6">

  {{-- Filter tabs --}}
  <div class="bg-white border border-kborder rounded-xl overflow-hidden">
    <div class="border-b border-kborder bg-kbg">
      <div class="flex gap-1 p-1">
        <a href="{{ route('admin.comments.index') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                  {{ !request('filter') ? 'bg-white text-royal shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
          All Comments
        </a>
        <a href="{{ route('admin.comments.index', ['filter' => 'pending']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center gap-2
                  {{ request('filter') === 'pending' ? 'bg-white text-royal shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
          <span>Pending</span>
          @php $pendingCount = \App\Models\Comment::where('is_approved', false)->count(); @endphp
          @if($pendingCount > 0)
            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
          @endif
        </a>
        <a href="{{ route('admin.comments.index', ['filter' => 'approved']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                  {{ request('filter') === 'approved' ? 'bg-white text-royal shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
          Approved
        </a>
      </div>
    </div>

    {{-- Bulk actions bar --}}
    @if($comments->count() > 0)
    <div class="px-5 py-3 border-b border-kborder bg-gray-50 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <input type="checkbox" id="selectAll" class="rounded border-kborder text-royal focus:ring-royal/20">
        <label for="selectAll" class="text-xs text-gray-600">Select all</label>
      </div>
      <div class="flex gap-2" id="bulkActions" style="display: none;">
        <button type="button" id="bulkApproveBtn" 
                class="px-3 py-1 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
          <i class="fas fa-check mr-1"></i> Approve Selected
        </button>
        <button type="button" id="bulkDeleteBtn" 
                class="px-3 py-1 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
          <i class="fas fa-trash mr-1"></i> Delete Selected
        </button>
      </div>
    </div>
    @endif

    {{-- Comments list header --}}
    <div class="px-5 py-3.5 border-b border-kborder bg-kbg grid grid-cols-12 text-xs font-semibold text-muted uppercase tracking-wide">
      <div class="col-span-1">
        <input type="checkbox" id="selectAllHeader" class="rounded border-kborder text-royal focus:ring-royal/20">
      </div>
      <div class="col-span-3">Commenter</div>
      <div class="col-span-4">Comment</div>
      <div class="col-span-2">Post</div>
      <div class="col-span-1 text-center">Status</div>
      <div class="col-span-1 text-right">Actions</div>
    </div>

    {{-- Comments list --}}
    <div class="divide-y divide-kborder">
      @forelse($comments as $comment)
      <div class="px-5 py-4 grid grid-cols-12 items-start gap-2 hover:bg-kbg transition-colors">
        {{-- Checkbox --}}
        <div class="col-span-1 pt-1">
          <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" 
                 class="comment-checkbox rounded border-kborder text-royal focus:ring-royal/20">
        </div>

        {{-- Commenter info --}}
        <div class="col-span-3">
          <div class="flex items-center gap-2">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user_name ?? $comment->user->name ?? 'Anonymous') }}&background=random&size=32" 
                 class="w-8 h-8 rounded-full shrink-0" alt="">
            <div class="min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ $comment->user_name ?? $comment->user->name ?? 'Anonymous' }}</p>
              @if($comment->user_email)
                <p class="text-xs text-muted truncate">{{ $comment->user_email }}</p>
              @endif
              <p class="text-xs text-muted mt-0.5">{{ $comment->created_at->format('M d, Y H:i') }}</p>
            </div>
          </div>
        </div>

        {{-- Comment content --}}
        <div class="col-span-4">
          <p class="text-sm text-gray-700 line-clamp-3">{{ $comment->content }}</p>
          @if(strlen($comment->content) > 150)
            <button class="text-xs text-royal hover:underline mt-1 expand-comment" data-id="{{ $comment->id }}">Read more</button>
          @endif
        </div>

        {{-- Post --}}
        <div class="col-span-2">
          <a href="{{ route('posts.show', $comment->post) }}" target="_blank" 
             class="text-sm text-royal hover:underline break-words">
            {{ Str::limit($comment->post->title, 40) }}
          </a>
        </div>

        {{-- Status badge --}}
        <div class="col-span-1 text-center">
          @if($comment->is_approved)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              <i class="fas fa-check-circle text-[10px]"></i> Approved
            </span>
          @else
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
              <i class="fas fa-clock text-[10px]"></i> Pending
            </span>
          @endif
        </div>

        {{-- Actions --}}
        <div class="col-span-1 flex justify-end gap-2">
          @if(!$comment->is_approved)
          <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="text-green-600 hover:text-green-700 transition-colors text-sm" title="Approve">
              <i class="fas fa-check-circle"></i>
            </button>
          </form>
          @else
          <form action="{{ route('admin.comments.disapprove', $comment) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="text-yellow-600 hover:text-yellow-700 transition-colors text-sm" title="Unapprove">
              <i class="fas fa-times-circle"></i>
            </button>
          </form>
          @endif
          
          <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline"
                onsubmit="return confirm('Delete this comment?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-muted hover:text-red-500 transition-colors text-sm" title="Delete">
              <i class="fas fa-trash"></i>
            </button>
          </form>
        </div>
      </div>

      {{-- Expanded comment modal content (hidden) --}}
      @if(strlen($comment->content) > 150)
      <div id="commentModal{{ $comment->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-lg w-full max-h-[80vh] overflow-auto">
          <div class="p-5">
            <div class="flex justify-between items-start mb-3">
              <h3 class="text-lg font-semibold">Full Comment</h3>
              <button onclick="closeModal({{ $comment->id }})" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $comment->content }}</p>
            <div class="mt-4 pt-3 border-t border-kborder flex justify-end">
              <button onclick="closeModal({{ $comment->id }})" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
      @endif
      @empty
      <div class="px-5 py-12 text-center text-sm text-muted italic">
        <i class="fas fa-comments text-4xl mb-3 opacity-30 block"></i>
        No comments found.
      </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    @if($comments->hasPages())
    <div class="px-5 py-3 border-t border-kborder bg-kbg">
      {{ $comments->withQueryString()->links() }}
    </div>
    @endif
  </div>

  {{-- Quick stats card --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white border border-kborder rounded-xl p-5">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs text-muted uppercase tracking-wide">Total Comments</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\Comment::count() }}</p>
        </div>
        <i class="fas fa-comments text-3xl text-gray-300"></i>
      </div>
    </div>

    <div class="bg-white border border-kborder rounded-xl p-5">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs text-muted uppercase tracking-wide">Pending Approval</p>
          <p class="text-2xl font-bold text-yellow-600 mt-1">{{ \App\Models\Comment::where('is_approved', false)->count() }}</p>
        </div>
        <i class="fas fa-clock text-3xl text-yellow-300"></i>
      </div>
    </div>

    <div class="bg-white border border-kborder rounded-xl p-5">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs text-muted uppercase tracking-wide">Approved</p>
          <p class="text-2xl font-bold text-green-600 mt-1">{{ \App\Models\Comment::where('is_approved', true)->count() }}</p>
        </div>
        <i class="fas fa-check-circle text-3xl text-green-300"></i>
      </div>
    </div>
  </div>

</div>

{{-- Hidden form for bulk actions --}}
<form id="bulkForm" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
  // Select all functionality
  const selectAllCheckbox = document.getElementById('selectAll');
  const selectAllHeader = document.getElementById('selectAllHeader');
  const commentCheckboxes = document.querySelectorAll('.comment-checkbox');
  const bulkActions = document.getElementById('bulkActions');
  const bulkApproveBtn = document.getElementById('bulkApproveBtn');
  const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
  const bulkForm = document.getElementById('bulkForm');

  function updateBulkActions() {
    const checked = document.querySelectorAll('.comment-checkbox:checked').length;
    if (checked > 0) {
      bulkActions.style.display = 'flex';
    } else {
      bulkActions.style.display = 'none';
    }
  }

  function toggleSelectAll(source) {
    commentCheckboxes.forEach(cb => {
      cb.checked = source.checked;
    });
    updateBulkActions();
  }

  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
      toggleSelectAll(this);
      if (selectAllHeader) selectAllHeader.checked = this.checked;
    });
  }

  if (selectAllHeader) {
    selectAllHeader.addEventListener('change', function() {
      toggleSelectAll(this);
      if (selectAllCheckbox) selectAllCheckbox.checked = this.checked;
    });
  }

  commentCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkActions);
  });

  // Bulk approve
  if (bulkApproveBtn) {
    bulkApproveBtn.addEventListener('click', function() {
      const selected = Array.from(document.querySelectorAll('.comment-checkbox:checked')).map(cb => cb.value);
      if (selected.length === 0) return;
      
      if (confirm(`Approve ${selected.length} comment(s)?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.comments.bulk-approve") }}';
        form.innerHTML = `
          @csrf
          <input type="hidden" name="comment_ids" value="${JSON.stringify(selected)}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  // Bulk delete
  if (bulkDeleteBtn) {
    bulkDeleteBtn.addEventListener('click', function() {
      const selected = Array.from(document.querySelectorAll('.comment-checkbox:checked')).map(cb => cb.value);
      if (selected.length === 0) return;
      
      if (confirm(`Delete ${selected.length} comment(s)? This cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.comments.bulk-delete") }}';
        form.innerHTML = `
          @csrf
          @method('DELETE')
          <input type="hidden" name="comment_ids" value="${JSON.stringify(selected)}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  // Expand comment modal
  function closeModal(id) {
    document.getElementById(`commentModal${id}`).classList.add('hidden');
  }

  window.closeModal = closeModal;

  document.querySelectorAll('.expand-comment').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.dataset.id;
      document.getElementById(`commentModal${id}`).classList.remove('hidden');
    });
  });

  // Close modal when clicking outside
  document.querySelectorAll('[id^="commentModal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
      if (e.target === this) {
        this.classList.add('hidden');
      }
    });
  });
</script>
@endpush