@extends('layouts.admin')

@section('content')
<div class="px-6 lg:px-8 py-6">

    {{-- ── PAGE HEADER ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-display font-semibold text-2xl text-gray-900 leading-tight">New Article</h1>
            <p class="text-sm text-gray-500 mt-0.5">Fill in the details below to publish or save a draft</p>
        </div>
        <a href="{{ route('dashboard.posts.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-colors shrink-0 bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Articles
        </a>
    </div>

    {{-- ── VALIDATION ERRORS ── --}}
    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-4 rounded-xl mb-5 text-sm bg-red-50 text-red-800 border border-red-200">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p class="font-semibold mb-1">Please fix the following errors:</p>
                <ul class="space-y-0.5 text-[13px] list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ── FORM ── --}}
    <form action="{{ route('dashboard.posts.store') }}" method="POST" enctype="multipart/form-data" id="article-form">
        @csrf

        <div class="flex flex-col xl:flex-row gap-5 items-start">

            {{-- ════════ LEFT: main content ════════ --}}
            <div class="flex-1 min-w-0 space-y-4">

                {{-- Article Title --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <label for="title" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2">
                        Headline <span class="text-red-500 normal-case font-normal">required</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="title"
                           value="{{ old('title') }}"
                           placeholder="Write a compelling headline…"
                           class="w-full text-xl font-display font-semibold text-gray-900 placeholder-gray-300 outline-none border-none bg-transparent"
                           required>
                    <div class="mt-3 pt-3 border-t border-gray-50">
                        <label class="block text-[10.5px] font-semibold uppercase tracking-wider text-gray-500 mb-1.5">
                            URL Slug
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">/news/</span>
                            <input type="text"
                                   name="slug"
                                   id="slug"
                                   value="{{ old('slug') }}"
                                   class="flex-1 text-xs font-mono px-2.5 py-1.5 rounded-lg outline-none transition-all bg-gray-50 border border-gray-200 text-gray-700"
                                   readonly>
                            <button type="button" onclick="enableSlugEditing()"
                                    class="text-[10.5px] font-medium px-2.5 py-1.5 rounded-lg transition-colors bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200">
                                Edit
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Excerpt --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <label for="excerpt" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2.5">
                        Excerpt / Standfirst
                    </label>
                    <textarea name="excerpt"
                              id="excerpt"
                              rows="3"
                              maxlength="255"
                              placeholder="A concise summary shown in article previews and search results…"
                              class="w-full text-sm text-gray-900 placeholder-gray-300 outline-none border-none bg-transparent resize-none leading-relaxed"
                              oninput="updateExcerptCount()">{{ old('excerpt') }}</textarea>
                    <p class="text-[10.5px] text-gray-500 mt-2 flex justify-between">
                        <span>Appears in article cards and SEO meta descriptions</span>
                        <span><span id="excerpt-count">{{ strlen(old('excerpt', '')) }}</span>/255</span>
                    </p>
                </div>

                {{-- Body Content --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <label for="content" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            Body Content <span class="text-red-500 normal-case font-normal">required</span>
                        </label>
                        <div class="flex items-center gap-1 text-[10px]">
                            <button type="button" onclick="setContentMode('html')" id="mode-html"
                                    class="px-2.5 py-1 rounded-md font-semibold transition-all bg-red-600 text-white">HTML</button>
                            <button type="button" onclick="setContentMode('md')" id="mode-md"
                                    class="px-2.5 py-1 rounded-md font-semibold transition-all bg-gray-100 text-gray-600">Markdown</button>
                        </div>
                    </div>

                    {{-- Minimal toolbar --}}
                    <div class="flex items-center gap-1 pb-2.5 mb-2.5 border-b border-gray-100 flex-wrap">
                        @foreach([
                            ['B', 'font-bold', 'bold'],
                            ['I', 'italic', 'italic'],
                            ['U', 'underline', 'underline'],
                        ] as [$lbl, $cls, $cmd])
                            <button type="button"
                                    onclick="formatText('{{ $cmd }}')"
                                    class="w-7 h-7 flex items-center justify-center rounded text-xs transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200 {{ $cls }}">
                                {{ $lbl }}
                            </button>
                        @endforeach
                        <div class="w-px h-4 bg-gray-200 mx-1"></div>
                        <button type="button"
                                onclick="insertLink()"
                                class="w-7 h-7 flex items-center justify-center rounded text-xs transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200"
                                title="Insert link">
                            <i class="fas fa-link text-[10px]"></i>
                        </button>
                        <button type="button"
                                onclick="insertImage()"
                                class="w-7 h-7 flex items-center justify-center rounded text-xs transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200"
                                title="Insert image">
                            <i class="fas fa-image text-[10px]"></i>
                        </button>
                        <button type="button"
                                onclick="formatText('insertUnorderedList')"
                                class="w-7 h-7 flex items-center justify-center rounded text-xs transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200"
                                title="Bullet list">
                            <i class="fas fa-list-ul text-[10px]"></i>
                        </button>
                        <button type="button"
                                onclick="formatText('formatBlock', 'blockquote')"
                                class="w-7 h-7 flex items-center justify-center rounded text-xs transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200"
                                title="Blockquote">
                            <i class="fas fa-quote-left text-[10px]"></i>
                        </button>
                    </div>

                    <textarea name="content"
                              id="content"
                              rows="18"
                              class="w-full text-sm text-gray-900 outline-none border-none bg-transparent resize-y leading-relaxed font-mono"
                              placeholder="Write your article body here… Supports HTML and Markdown."
                              required>{{ old('content') }}</textarea>
                </div>

                {{-- Featured Image with Upload Status --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-3">
                        Featured Image
                    </label>
                    
                    {{-- Upload Status Notification --}}
                    <div id="upload-status" class="hidden mb-3 rounded-lg p-3 text-sm"></div>
                    
                    <div class="flex flex-col items-center justify-center gap-2 rounded-xl cursor-pointer transition-all py-8 border-2 border-dashed border-gray-200 bg-gray-50 hover:border-red-500 hover:bg-red-50"
                         id="image-drop"
                         onclick="document.getElementById('featured_image').click()">
                        <div id="img-preview-wrap" class="hidden w-full px-4">
                            <img id="img-preview" src="#" alt="Preview"
                                 class="max-h-48 mx-auto rounded-lg object-cover">
                        </div>
                        <div id="img-placeholder">
                            <i class="fas fa-cloud-arrow-up text-2xl text-gray-400 block text-center mb-2"></i>
                            <p class="text-sm font-medium text-gray-500 text-center">Drag & drop or <span class="text-red-600">browse</span></p>
                            <p class="text-[11px] text-gray-400 text-center mt-1">JPG, PNG, WebP — max 2 MB</p>
                        </div>
                    </div>
                    <input type="file"
                           name="featured_image"
                           id="featured_image"
                           accept="image/*"
                           class="sr-only"
                           onchange="handleImageUpload(this)">
                    <div id="upload-progress" class="hidden mt-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-600">Uploading...</span>
                            <span id="upload-percent" class="text-xs text-gray-500">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div id="upload-progress-bar" class="bg-red-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                    <p id="img-filename" class="text-[11px] text-gray-500 mt-2 hidden text-center"></p>
                </div>

            </div>

            {{-- ════════ RIGHT: sidebar settings ════════ --}}
            <div class="w-full xl:w-72 space-y-4 shrink-0">

                {{-- Publish panel --}}
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-4 py-3.5 flex items-center justify-between border-b border-gray-100 bg-gray-50">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Publish</span>
                        <span id="status-badge"
                              class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">Draft</span>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-900 mb-1.5">Status</label>
                            <select name="status" id="status"
                                    class="w-full text-sm px-3 py-2 rounded-lg outline-none transition-all bg-gray-50 border border-gray-200 text-gray-700 focus:border-red-500 focus:bg-white"
                                    onchange="updateStatusBadge(this.value)"
                                    required>
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>📝 Draft</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>✅ Published</option>
                                <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>🗓 Scheduled</option>
                                <option value="breaking" {{ old('status') === 'breaking' ? 'selected' : '' }}>⚡ Breaking News</option>
                            </select>
                        </div>

                        {{-- Schedule date (shown when scheduled) --}}
                        <div id="schedule-wrap" class="hidden">
                            <label class="block text-xs font-semibold text-gray-900 mb-1.5">Publish at</label>
                            <input type="datetime-local" name="publish_at" value="{{ old('publish_at') }}"
                                   class="w-full text-sm px-3 py-2 rounded-lg outline-none bg-gray-50 border border-gray-200 text-gray-700 focus:border-red-500 focus:bg-white">
                        </div>

                        <div class="pt-2 flex flex-col gap-2">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold text-white transition-all bg-red-600 hover:bg-red-700 shadow-lg shadow-red-200">
                                <i class="fas fa-paper-plane text-xs"></i>
                                Publish Article
                            </button>
                            <button type="button"
                                    onclick="saveAsDraft()"
                                    class="w-full py-2 rounded-xl text-sm font-semibold transition-colors bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200">
                                Save as Draft
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Category --}}
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <label for="category_id" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2.5">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id"
                            class="w-full text-sm px-3 py-2 rounded-lg outline-none bg-gray-50 border border-gray-200 text-gray-700 focus:border-red-500 focus:bg-white"
                            required>
                        <option value="">Select a category…</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tags --}}
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-2.5">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">Tags</label>
                        <a href="{{ route('dashboard.tags.index') }}"
                           class="text-[10px] font-medium text-red-600 hover:text-red-700">
                            + New tag
                        </a>
                    </div>

                    @if($allTags->isEmpty())
                        <p class="text-xs text-gray-500 italic py-2">No tags yet. <a href="{{ route('dashboard.tags.index') }}" class="text-red-600">Create one →</a></p>
                    @else
                        {{-- Searchable tag checkboxes --}}
                        <input type="text" id="tag-search"
                               placeholder="Filter tags…"
                               class="w-full text-xs px-2.5 py-1.5 rounded-lg mb-2 outline-none bg-gray-50 border border-gray-200 focus:border-red-500 focus:bg-white"
                               oninput="filterTags(this.value)">
                        <div id="tags-list" class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            @foreach($allTags as $tag)
                                @php $checked = old('tags') && in_array($tag->id, old('tags')); @endphp
                                <label class="tag-item flex items-center gap-2 cursor-pointer py-1 px-2 rounded-lg transition-colors hover:bg-gray-50"
                                       data-name="{{ strtolower($tag->name) }}">
                                    <input type="checkbox"
                                           name="tags[]"
                                           value="{{ $tag->id }}"
                                           class="rounded text-red-600 focus:ring-red-500"
                                           {{ $checked ? 'checked' : '' }}>
                                    <span class="text-xs font-medium text-gray-900">#{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2">Select as many as apply</p>
                    @endif
                </div>

                {{-- SEO Preview --}}
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-3">SEO Preview</p>
                    <div class="rounded-lg p-3 text-[11.5px] bg-gray-50 border border-gray-200">
                        <p class="font-semibold text-blue-700 leading-tight mb-0.5" id="seo-title">Article title will appear here</p>
                        <p class="text-green-700 text-[10.5px] mb-0.5">yournewssite.com/news/<span id="seo-slug">article-slug</span></p>
                        <p class="text-gray-500 text-[10.5px] leading-relaxed" id="seo-excerpt">Article excerpt will appear as the meta description…</p>
                    </div>
                </div>

                {{-- Danger zone --}}
                <div class="rounded-xl border p-4 border-red-200 bg-red-50">
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-2 text-red-800">Reset</p>
                    <button type="button"
                            class="w-full text-xs font-semibold py-2 rounded-lg transition-colors bg-red-100 text-red-700 border border-red-200 hover:bg-red-200"
                            onclick="clearForm()">
                        <i class="fas fa-rotate-left mr-1 text-[10px]"></i>
                        Clear form
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// ── Slug auto-generator ──────────────────────────────────
const titleInput = document.getElementById('title');
const slugInput = document.getElementById('slug');

if (titleInput && slugInput) {
    titleInput.addEventListener('input', function () {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        
        if (slugInput.hasAttribute('readonly')) {
            slugInput.value = slug;
        }
        
        updateSEOPreview(this.value, slug);
    });
}

function updateSEOPreview(title, slug) {
    const seoTitle = document.getElementById('seo-title');
    const seoSlug = document.getElementById('seo-slug');
    
    if (seoTitle) seoTitle.textContent = title || 'Article title will appear here';
    if (seoSlug) seoSlug.textContent = slug || 'article-slug';
}

function enableSlugEditing() {
    if (slugInput) {
        slugInput.removeAttribute('readonly');
        slugInput.focus();
    }
}

// ── Excerpt counter ──────────────────────────────────────
function updateExcerptCount() {
    const excerpt = document.getElementById('excerpt');
    const countSpan = document.getElementById('excerpt-count');
    if (excerpt && countSpan) {
        countSpan.textContent = excerpt.value.length;
        const seoExcerpt = document.getElementById('seo-excerpt');
        if (seoExcerpt) {
            seoExcerpt.textContent = excerpt.value || 'Article excerpt will appear as the meta description…';
        }
    }
}

// ── Status badge updater ─────────────────────────────────
const statusStyles = {
    draft:     { bg:'bg-gray-100', color:'text-gray-600', label:'Draft' },
    published: { bg:'bg-green-100', color:'text-green-700', label:'Published' },
    scheduled: { bg:'bg-yellow-100', color:'text-yellow-700', label:'Scheduled' },
    breaking:  { bg:'bg-red-100', color:'text-red-700', label:'Breaking' },
};

function updateStatusBadge(val) {
    const s = statusStyles[val] || statusStyles.draft;
    const badge = document.getElementById('status-badge');
    if (badge) {
        badge.textContent = s.label;
        badge.className = `text-[10px] font-bold px-2 py-0.5 rounded-full ${s.bg} ${s.color}`;
    }
    
    const scheduleWrap = document.getElementById('schedule-wrap');
    if (scheduleWrap) {
        scheduleWrap.classList.toggle('hidden', val !== 'scheduled');
    }
}

// Initialize status badge on page load
const statusSelect = document.getElementById('status');
if (statusSelect) {
    updateStatusBadge(statusSelect.value);
}

// ── Content mode toggle ──────────────────────────────────
function setContentMode(mode) {
    const htmlBtn = document.getElementById('mode-html');
    const mdBtn = document.getElementById('mode-md');
    
    if (htmlBtn && mdBtn) {
        if (mode === 'html') {
            htmlBtn.className = 'px-2.5 py-1 rounded-md font-semibold transition-all bg-red-600 text-white';
            mdBtn.className = 'px-2.5 py-1 rounded-md font-semibold transition-all bg-gray-100 text-gray-600';
        } else {
            htmlBtn.className = 'px-2.5 py-1 rounded-md font-semibold transition-all bg-gray-100 text-gray-600';
            mdBtn.className = 'px-2.5 py-1 rounded-md font-semibold transition-all bg-red-600 text-white';
        }
    }
}

// ── Text formatting helper ───────────────────────────────
function formatText(command, value = null) {
    const contentField = document.getElementById('content');
    if (contentField && document.activeElement === contentField) {
        const start = contentField.selectionStart;
        const end = contentField.selectionEnd;
        const text = contentField.value;
        const selectedText = text.substring(start, end);
        
        if (command === 'bold') {
            const replacement = `**${selectedText}**`;
            contentField.value = text.substring(0, start) + replacement + text.substring(end);
            contentField.selectionStart = start;
            contentField.selectionEnd = start + replacement.length;
        } else if (command === 'italic') {
            const replacement = `*${selectedText}*`;
            contentField.value = text.substring(0, start) + replacement + text.substring(end);
            contentField.selectionStart = start;
            contentField.selectionEnd = start + replacement.length;
        } else if (command === 'underline') {
            const replacement = `<u>${selectedText}</u>`;
            contentField.value = text.substring(0, start) + replacement + text.substring(end);
            contentField.selectionStart = start;
            contentField.selectionEnd = start + replacement.length;
        } else if (command === 'insertUnorderedList') {
            const lines = selectedText.split('\n');
            const bulletList = lines.map(line => `- ${line}`).join('\n');
            contentField.value = text.substring(0, start) + bulletList + text.substring(end);
        } else if (command === 'formatBlock' && value === 'blockquote') {
            const replacement = `> ${selectedText}`;
            contentField.value = text.substring(0, start) + replacement + text.substring(end);
        }
        contentField.focus();
    }
}

// ── Insert link helper ───────────────────────────────────
function insertLink() {
    const contentField = document.getElementById('content');
    if (contentField && document.activeElement === contentField) {
        const url = prompt('Enter URL:', 'https://');
        if (url) {
            const start = contentField.selectionStart;
            const end = contentField.selectionEnd;
            const selectedText = contentField.value.substring(start, end) || 'link text';
            const linkMarkdown = `[${selectedText}](${url})`;
            contentField.value = contentField.value.substring(0, start) + linkMarkdown + contentField.value.substring(end);
            contentField.focus();
        }
    } else {
        showNotification('Please click in the content area first', 'warning');
    }
}

// ── Insert image helper ──────────────────────────────────
function insertImage() {
    const contentField = document.getElementById('content');
    if (contentField && document.activeElement === contentField) {
        const imageUrl = prompt('Enter image URL:', 'https://');
        if (imageUrl) {
            const imageMarkdown = `![alt text](${imageUrl})`;
            const start = contentField.selectionStart;
            contentField.value = contentField.value.substring(0, start) + imageMarkdown + contentField.value.substring(contentField.selectionEnd);
            contentField.focus();
        }
    } else {
        showNotification('Please click in the content area first', 'warning');
    }
}

// ── Image Upload with Status Notifications ───────────────
function handleImageUpload(input) {
    if (!input.files || !input.files.length) return;
    
    const file = input.files[0];
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Invalid file type. Please upload JPG, PNG, or WebP images only.', 'error');
        input.value = '';
        return;
    }
    
    // Validate file size (max 2MB)
    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
        showNotification('File too large. Maximum size is 2MB.', 'error');
        input.value = '';
        return;
    }
    
    // Show upload progress
    showUploadProgress(true);
    updateUploadProgress(0);
    showNotification('Uploading image...', 'info');
    
    // Simulate upload progress (in real scenario, you'd use AJAX/Fetch)
    let progress = 0;
    const interval = setInterval(() => {
        progress += 10;
        updateUploadProgress(progress);
        
        if (progress >= 100) {
            clearInterval(interval);
            // Simulate successful upload
            setTimeout(() => {
                previewImage(input);
                showUploadProgress(false);
                showNotification('Image uploaded successfully!', 'success');
            }, 200);
        }
    }, 150);
}

function previewImage(input) {
    if (!input.files || !input.files.length) return;
    
    const file = input.files[0];
    const url = URL.createObjectURL(file);
    
    const previewImg = document.getElementById('img-preview');
    const previewWrap = document.getElementById('img-preview-wrap');
    const placeholder = document.getElementById('img-placeholder');
    const filenameSpan = document.getElementById('img-filename');
    
    if (previewImg) previewImg.src = url;
    if (previewWrap) previewWrap.classList.remove('hidden');
    if (placeholder) placeholder.classList.add('hidden');
    if (filenameSpan) {
        filenameSpan.textContent = file.name;
        filenameSpan.classList.remove('hidden');
    }
}

function showUploadProgress(show) {
    const progressDiv = document.getElementById('upload-progress');
    if (progressDiv) {
        if (show) {
            progressDiv.classList.remove('hidden');
        } else {
            progressDiv.classList.add('hidden');
        }
    }
}

function updateUploadProgress(percent) {
    const progressBar = document.getElementById('upload-progress-bar');
    const percentSpan = document.getElementById('upload-percent');
    
    if (progressBar) progressBar.style.width = `${percent}%`;
    if (percentSpan) percentSpan.textContent = `${percent}%`;
}

function showNotification(message, type = 'info') {
    const statusDiv = document.getElementById('upload-status');
    if (!statusDiv) return;
    
    // Set styles based on notification type
    const styles = {
        success: 'bg-green-50 text-green-800 border border-green-200',
        error: 'bg-red-50 text-red-800 border border-red-200',
        warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
        info: 'bg-blue-50 text-blue-800 border border-blue-200'
    };
    
    statusDiv.className = `mb-3 rounded-lg p-3 text-sm ${styles[type]}`;
    statusDiv.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    statusDiv.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        statusDiv.classList.add('hidden');
    }, 5000);
}

// ── Tag search filter ────────────────────────────────────
function filterTags(query) {
    query = query.toLowerCase();
    const tagItems = document.querySelectorAll('.tag-item');
    tagItems.forEach(el => {
        const name = el.getAttribute('data-name');
        if (name && name.includes(query)) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });
}

// ── Save as draft ────────────────────────────────────────
function saveAsDraft() {
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.value = 'draft';
    }
    document.getElementById('article-form').submit();
}

// ── Clear form ───────────────────────────────────────────
function clearForm() {
    if (confirm('Clear all fields?')) {
        const form = document.getElementById('article-form');
        if (form) {
            form.reset();
            
            // Reset previews and counters
            const previewWrap = document.getElementById('img-preview-wrap');
            const placeholder = document.getElementById('img-placeholder');
            const filenameSpan = document.getElementById('img-filename');
            const uploadStatus = document.getElementById('upload-status');
            
            if (previewWrap) previewWrap.classList.add('hidden');
            if (placeholder) placeholder.classList.remove('hidden');
            if (filenameSpan) filenameSpan.classList.add('hidden');
            if (uploadStatus) uploadStatus.classList.add('hidden');
            
            const excerptCount = document.getElementById('excerpt-count');
            if (excerptCount) excerptCount.textContent = '0';
            
            updateSEOPreview('', 'article-slug');
            showNotification('Form cleared successfully', 'success');
        }
    }
}

// ── Drag and drop for images ─────────────────────────────
const dropZone = document.getElementById('image-drop');
const fileInput = document.getElementById('featured_image');

if (dropZone && fileInput) {
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-red-500', 'bg-red-50');
        dropZone.classList.remove('border-gray-200', 'bg-gray-50');
    });
    
    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-red-500', 'bg-red-50');
        dropZone.classList.add('border-gray-200', 'bg-gray-50');
    });
    
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-red-500', 'bg-red-50');
        dropZone.classList.add('border-gray-200', 'bg-gray-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleImageUpload(fileInput);
        }
    });
}

// Initialize excerpt count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateExcerptCount();
});
</script>
@endpush

@endsection