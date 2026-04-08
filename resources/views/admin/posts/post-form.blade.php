{{-- resources/views/admin/posts/form.blade.php --}}
@extends('layouts.admin')

@section('title', $post->exists ? 'Edit: ' . $post->title : 'New Post')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">

  <div class="flex items-center justify-between mb-8">
    <h1 class="font-display font-semibold text-2xl text-navy">
      {{ $post->exists ? 'Edit Post' : 'New Post' }}
    </h1>
    @if($post->exists && $post->status === 'published')
      <a href="{{ route('post.show', $post->slug) }}" target="_blank"
         class="text-sm text-royal hover:underline flex items-center gap-1">
        <i class="fas fa-external-link-alt text-xs"></i> View live
      </a>
    @endif
  </div>

  <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method($method)

    {{-- ── Two-column layout ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- Left: main content (2/3 width) --}}
      <div class="lg:col-span-2 space-y-5">

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
          <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                 class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal focus:ring-2 focus:ring-royal/10 outline-none @error('title') border-red-400 @enderror"
                 placeholder="Compelling, keyword-rich headline" />
          @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
          <div class="flex items-center border border-kborder rounded-lg overflow-hidden focus-within:border-royal focus-within:ring-2 focus-within:ring-royal/10">
            <span class="px-3 py-2 text-sm text-muted bg-kbg border-r border-kborder shrink-0">/news/</span>
            <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                   class="flex-1 px-3 py-2 text-sm outline-none bg-white"
                   placeholder="auto-generated-from-title" />
          </div>
          <p class="text-xs text-muted mt-1">Leave blank to auto-generate from title. Cannot be changed after publishing.</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
          <textarea name="excerpt" rows="2"
                    class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal focus:ring-2 focus:ring-royal/10 outline-none"
                    placeholder="1–2 sentence summary shown in cards and as fallback meta description (max 500 chars)">{{ old('excerpt', $post->excerpt) }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
          {{-- In production: replace textarea with TipTap / Quill / Trix --}}
          <textarea name="content" rows="20" required
                    class="w-full border border-kborder rounded-lg px-3 py-2 text-sm font-mono focus:border-royal focus:ring-2 focus:ring-royal/10 outline-none @error('content') border-red-400 @enderror"
                    placeholder="HTML content…">{{ old('content', $post->content) }}</textarea>
          @error('content')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- ── SEO panel ─────────────────────────────────────────────────── --}}
        <details class="border border-kborder rounded-xl overflow-hidden" open>
          <summary class="px-5 py-3 bg-kbg text-sm font-semibold text-gray-800 cursor-pointer flex items-center gap-2">
            <i class="fas fa-search text-royal text-xs"></i> SEO Settings
          </summary>
          <div class="p-5 space-y-4">

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Meta Title
                <span class="font-normal text-muted ml-1" id="meta-title-count">0/70</span>
              </label>
              <input type="text" name="meta_title" id="meta_title"
                     value="{{ old('meta_title', $post->meta_title) }}" maxlength="70"
                     class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal focus:ring-2 focus:ring-royal/10 outline-none"
                     placeholder="Falls back to post title if empty" />
              <p class="text-xs text-muted mt-1">Ideal: 50–60 characters. Appears in browser tab and Google results.</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Meta Description
                <span class="font-normal text-muted ml-1" id="meta-desc-count">0/160</span>
              </label>
              <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                        class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal focus:ring-2 focus:ring-royal/10 outline-none"
                        placeholder="Compelling snippet shown in Google. Falls back to excerpt.">{{ old('meta_description', $post->meta_description) }}</textarea>
              <p class="text-xs text-muted mt-1">Ideal: 120–155 characters. Include your primary keyword naturally.</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
              <input type="text" name="meta_keywords"
                     value="{{ old('meta_keywords', $post->meta_keywords) }}"
                     class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal focus:ring-2 focus:ring-royal/10 outline-none"
                     placeholder="keyword one, keyword two, keyword three" />
              <p class="text-xs text-muted mt-1">Comma-separated. Used in JSON-LD, not Google's ranking signal but helps Bing.</p>
            </div>

            {{-- Live SERP preview --}}
            <div class="border border-kborder rounded-lg p-4 bg-white">
              <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-3">Google Preview</p>
              <p class="text-xs text-green-700 mb-0.5">kusoma.co › news › <span id="preview-slug">{{ $post->slug ?: 'your-post-slug' }}</span></p>
              <p id="preview-title" class="text-[17px] text-[#1a0dab] font-medium leading-snug mb-1">{{ $post->seo_title ?: 'Your Post Title' }}</p>
              <p id="preview-desc" class="text-sm text-gray-600 leading-snug">{{ $post->seo_description ?: 'Your meta description will appear here in Google search results.' }}</p>
            </div>

          </div>
        </details>
      </div>

      {{-- Right: sidebar (1/3 width) --}}
      <div class="space-y-5">

        {{-- Publish panel --}}
        <div class="bg-white border border-kborder rounded-xl overflow-hidden">
          <div class="px-4 py-3 bg-kbg border-b border-kborder text-sm font-semibold text-gray-800">Publish</div>
          <div class="p-4 space-y-3">
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
              <select name="status"
                      class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal outline-none">
                <option value="draft"     {{ old('status', $post->status) === 'draft'     ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Publish Date</label>
              <input type="datetime-local" name="published_at"
                     value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                     class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal outline-none" />
              <p class="text-xs text-muted mt-1">Leave blank to publish immediately.</p>
            </div>
            <button type="submit"
                    class="w-full bg-royal hover:bg-navy text-white text-sm font-medium py-2 rounded-lg transition">
              {{ $post->exists ? 'Update Post' : 'Create Post' }}
            </button>
            @if($post->exists)
              <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
                    onsubmit="return confirm('Permanently delete this post?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full text-xs text-red-500 hover:underline mt-1">Delete post</button>
              </form>
            @endif
          </div>
        </div>

        {{-- Category --}}
        <div class="bg-white border border-kborder rounded-xl overflow-hidden">
          <div class="px-4 py-3 bg-kbg border-b border-kborder text-sm font-semibold text-gray-800">Category *</div>
          <div class="p-4">
            <select name="category_id" required
                    class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal outline-none @error('category_id') border-red-400 @enderror">
              <option value="">Select category…</option>
              @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Tags --}}
        <div class="bg-white border border-kborder rounded-xl overflow-hidden">
          <div class="px-4 py-3 bg-kbg border-b border-kborder text-sm font-semibold text-gray-800">Tags</div>
          <div class="p-4">
            <div class="flex flex-wrap gap-2">
              @foreach($tags as $tag)
              <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                       {{ in_array($tag->id, old('tag_ids', $post->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                       class="accent-royal" />
                {{ $tag->name }}
              </label>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Featured image --}}
        <div class="bg-white border border-kborder rounded-xl overflow-hidden">
          <div class="px-4 py-3 bg-kbg border-b border-kborder text-sm font-semibold text-gray-800">Featured Image</div>
          <div class="p-4">
            <input type="text" name="featured_image"
                   value="{{ old('featured_image', $post->featured_image) }}"
                   class="w-full border border-kborder rounded-lg px-3 py-2 text-sm focus:border-royal outline-none mb-2"
                   placeholder="images/posts/my-image.jpg" />
            @if($post->featured_image)
              <img src="{{ asset($post->featured_image) }}" alt="Featured"
                   class="w-full h-28 object-cover rounded-lg" />
            @endif
            <p class="text-xs text-muted mt-2">Path relative to <code>public/</code>. Used for OG image and article header.</p>
          </div>
        </div>

      </div>{{-- /sidebar --}}
    </div>{{-- /grid --}}
  </form>
</div>

<script>
  // ── Live SEO character counters + SERP preview ────────────────────────────
  const titleInput = document.getElementById('meta_title');
  const descInput  = document.getElementById('meta_description');
  const mainTitle  = document.querySelector('[name="title"]');
  const slugInput  = document.querySelector('[name="slug"]');

  function update() {
    document.getElementById('meta-title-count').textContent =
      `${titleInput.value.length}/70`;
    document.getElementById('meta-desc-count').textContent =
      `${descInput.value.length}/160`;
    document.getElementById('preview-title').textContent =
      titleInput.value || mainTitle.value || 'Your Post Title';
    document.getElementById('preview-desc').textContent =
      descInput.value || 'Your meta description will appear here.';
    document.getElementById('preview-slug').textContent =
      slugInput.value || mainTitle.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'your-post-slug';
  }

  [titleInput, descInput, mainTitle, slugInput].forEach(el => el?.addEventListener('input', update));
  update();
</script>
@endsection
