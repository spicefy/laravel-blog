<x-app-layout>
    {{-- resources/views/dashboard.blade.php --}}
    <x-slot name="title">Dashboard</x-slot>

    {{-- ── Stat cards ────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

      @php
        $stats = [
          ['label' => 'Published posts',  'value' => $publishedCount,  'icon' => 'fa-newspaper',   'color' => 'text-royal'],
          ['label' => 'Drafts',           'value' => $draftCount,      'icon' => 'fa-pen-to-square','color' => 'text-cent'],
          ['label' => 'Pending comments', 'value' => $pendingComments, 'icon' => 'fa-comments',     'color' => 'text-red-500'],
          ['label' => 'Total views',      'value' => number_format($totalViews), 'icon' => 'fa-eye','color' => 'text-cbiz'],
        ];
      @endphp

      @foreach($stats as $stat)
      <div class="bg-white border border-kborder rounded-xl px-5 py-4">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs text-muted mb-1">{{ $stat['label'] }}</p>
            <p class="font-display font-bold text-2xl text-navy">{{ $stat['value'] }}</p>
          </div>
          <i class="fas {{ $stat['icon'] }} {{ $stat['color'] }} text-lg mt-0.5"></i>
        </div>
      </div>
      @endforeach

    </div>

    {{-- ── Two-column: recent posts + pending comments ──────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- Recent posts --}}
      <div class="bg-white border border-kborder rounded-xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-kborder flex items-center justify-between">
          <h2 class="text-sm font-semibold text-gray-800">Recent posts</h2>
          <a href="{{ route('admin.posts.index') }}" class="text-xs text-royal hover:underline">View all →</a>
        </div>
        <div class="divide-y divide-kborder">
          @forelse($recentPosts as $post)
          <div class="px-5 py-3 flex items-start justify-between gap-3 hover:bg-kbg transition-colors">
            <div class="min-w-0">
              <a href="{{ route('admin.posts.edit', $post) }}"
                 class="text-[13px] font-medium text-kgreen hover:underline block truncate">
                {{ $post->title }}
              </a>
              <div class="flex gap-2 text-xs text-muted mt-0.5">
                <span class="text-{{ $post->category->css_suffix }} font-medium">{{ $post->category->name }}</span>
                <span>·</span>
                <span>{{ $post->author->name }}</span>
                <span>·</span>
                <time datetime="{{ $post->created_at->toIso8601String() }}">
                  {{ $post->created_at->diffForHumans() }}
                </time>
              </div>
            </div>
            <span class="shrink-0 text-[11px] font-medium px-2 py-0.5 rounded-full
              {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
              {{ ucfirst($post->status) }}
            </span>
          </div>
          @empty
          <p class="px-5 py-4 text-sm text-muted italic">No posts yet.</p>
          @endforelse
        </div>
      </div>

      {{-- Pending comments --}}
      <div class="bg-white border border-kborder rounded-xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-kborder flex items-center justify-between">
          <h2 class="text-sm font-semibold text-gray-800">
            Pending comments
            @if($pendingComments > 0)
              <span class="ml-1.5 bg-red-100 text-red-600 text-[11px] font-bold px-1.5 py-0.5 rounded-full">
                {{ $pendingComments }}
              </span>
            @endif
          </h2>
          <a href="{{ route('admin.comments.index') }}" class="text-xs text-royal hover:underline">Moderate →</a>
        </div>
        <div class="divide-y divide-kborder">
          @forelse($latestPendingComments as $comment)
          <div class="px-5 py-3 hover:bg-kbg transition-colors">
            <div class="flex items-start justify-between gap-2 mb-1">
              <span class="text-sm font-medium text-gray-900">{{ $comment->name }}</span>
              <time class="text-xs text-muted shrink-0">{{ $comment->created_at->diffForHumans() }}</time>
            </div>
            <p class="text-xs text-muted line-clamp-2 mb-2">{{ $comment->comment }}</p>
            <div class="flex gap-2">
              <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                @csrf @method('PATCH')
                <button class="text-xs text-green-600 hover:underline font-medium">Approve</button>
              </form>
              <span class="text-muted text-xs">·</span>
              <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                    onsubmit="return confirm('Delete this comment?')">
                @csrf @method('DELETE')
                <button class="text-xs text-red-500 hover:underline">Delete</button>
              </form>
            </div>
          </div>
          @empty
          <p class="px-5 py-4 text-sm text-muted italic">No pending comments. 🎉</p>
          @endforelse
        </div>
      </div>

    </div>

    {{-- ── Top posts by views ────────────────────────────────────────────────── --}}
    <div class="mt-6 bg-white border border-kborder rounded-xl overflow-hidden">
      <div class="px-5 py-3.5 border-b border-kborder">
        <h2 class="text-sm font-semibold text-gray-800">Top posts by views</h2>
      </div>
      <div class="divide-y divide-kborder">
        @foreach($topPosts as $i => $post)
        <div class="px-5 py-3 flex items-center gap-4 hover:bg-kbg transition-colors">
          <span class="font-display font-bold text-xl text-kborder w-6 shrink-0">{{ $i + 1 }}</span>
          <div class="flex-1 min-w-0">
            <a href="{{ route('admin.posts.edit', $post) }}"
               class="text-[13px] font-medium text-kgreen hover:underline truncate block">
              {{ $post->title }}
            </a>
            <span class="text-xs text-{{ $post->category->css_suffix }}">{{ $post->category->name }}</span>
          </div>
          <span class="text-sm font-semibold text-navy shrink-0">
            {{ number_format($post->view_count) }} <span class="text-xs font-normal text-muted">views</span>
          </span>
        </div>
        @endforeach
      </div>
    </div>

</x-app-layout>