@extends('layouts.admin')

@section('title', 'Dashboard 23')

@section('content')

{{-- ── Stat cards ───────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  @php
    $stats = [
      ['label' => 'Published posts',  'value' => $publishedCount ?? 0,  'icon' => 'fa-newspaper',   'color' => 'text-royal'],
      ['label' => 'Drafts',           'value' => $draftCount ?? 0,      'icon' => 'fa-pen-to-square','color' => 'text-cent'],
      ['label' => 'Pending comments', 'value' => $pendingComments ?? 0, 'icon' => 'fa-comments',     'color' => 'text-red-500'],
      ['label' => 'Total views',      'value' => number_format($totalViews ?? 0), 'icon' => 'fa-eye','color' => 'text-cbiz'],
    ];
  @endphp

  @foreach($stats as $stat)
  <div class="bg-white border border-kborder rounded-xl px-5 py-4 hover:shadow-md">
    <div class="flex justify-between">
      <div>
        <p class="text-xs text-muted">{{ $stat['label'] }}</p>
        <p class="font-bold text-2xl text-navy">{{ $stat['value'] }}</p>
      </div>
      <i class="fas {{ $stat['icon'] }} {{ $stat['color'] }}"></i>
    </div>
  </div>
  @endforeach
</div>

{{-- ── Two columns ─────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- Recent posts --}}
  <div class="bg-white border rounded-xl">
    <div class="p-4 border-b flex justify-between">
      <h2>Recent posts</h2>
      <a href="{{ route('admin.posts.index') }}">View all →</a>
    </div>

    @forelse($recentPosts ?? [] as $post)
    <div class="p-4 border-b flex justify-between">
      <div>
        <a href="{{ route('admin.posts.edit', $post) }}">
          {{ $post->title ?? 'Untitled' }}
        </a>
        <small class="block">
          {{ $post->author->name ?? 'Unknown' }} • 
          {{ $post->created_at?->diffForHumans() }}
        </small>
      </div>

      <span>
        {{ ucfirst($post->status ?? 'draft') }}
      </span>
    </div>
    @empty
    <div class="p-4 text-center">No posts yet</div>
    @endforelse
  </div>

  {{-- Comments --}}
  <div class="bg-white border rounded-xl">
    <div class="p-4 border-b flex justify-between">
      <h2>Pending comments</h2>
      <a href="{{ route('admin.comments.index') }}">Moderate →</a>
    </div>

    @forelse($latestPendingComments ?? [] as $comment)
    <div class="p-4 border-b">
      <strong>{{ $comment->name }}</strong>
      <p>{{ \Illuminate\Support\Str::limit($comment->comment, 100) }}</p>

      <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
        @csrf
        @method('PATCH')
        <button>Approve</button>
      </form>
    </div>
    @empty
    <div class="p-4 text-center">No comments</div>
    @endforelse
  </div>

</div>

{{-- ── Top posts ───────────────────────────────────────── --}}
@if(!empty($topPosts))
<div class="mt-6 bg-white border rounded-xl">
  <div class="p-4 border-b">
    <h2>Top posts</h2>
  </div>

  @foreach($topPosts as $i => $post)
  <div class="p-4 border-b flex justify-between">
    <span>{{ $i + 1 }}. {{ $post->title }}</span>
    <span>{{ number_format($post->view_count) }} views</span>
  </div>
  @endforeach
</div>
@endif

@endsection