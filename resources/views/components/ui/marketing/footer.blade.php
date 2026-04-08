{{-- resources/views/components/ui/marketing/footer.blade.php --}}
<footer class="border-t border-kborder bg-white mt-16">
  <div class="max-w-6xl mx-auto px-6 py-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">

      {{-- Brand --}}
      <div>
        <p class="font-display font-bold text-xl text-navy mb-2">Kusoma</p>
        <p class="text-sm text-muted leading-relaxed mb-4">
          Africa's education and technology newsroom. Trusted by students, teachers, and policy makers.
        </p>
        <a href="{{ route('feed.rss') }}"
           class="inline-flex items-center gap-2 text-xs text-royal border border-royal/30 rounded-lg px-3 py-1.5 hover:bg-klight transition-colors">
          <i class="fas fa-rss text-[10px]"></i> RSS Feed
        </a>
      </div>

      {{-- Categories --}}
      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-muted mb-3">Topics</h4>
        <ul class="space-y-2">
          @foreach(\App\Models\Category::all() as $cat)
          <li>
            <a href="{{ route('category.show', $cat->slug) }}"
               class="text-sm text-gray-700 hover:text-royal hover:underline">
              {{ $cat->name }}
            </a>
          </li>
          @endforeach
        </ul>
      </div>

      {{-- Popular tags --}}
      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-muted mb-3">Popular Tags</h4>
        <div class="flex flex-wrap gap-2">
          @foreach(\App\Models\Tag::withCount('posts')->orderByDesc('posts_count')->take(8)->get() as $tag)
          <a href="{{ route('tag.show', $tag->slug) }}"
             class="text-xs bg-kbg border border-kborder text-muted px-3 py-1 rounded-full hover:text-royal hover:border-royal/40 transition-colors">
            {{ $tag->name }}
          </a>
          @endforeach
        </div>
      </div>

      {{-- Legal / info --}}
      <div>
        <h4 class="text-xs font-semibold uppercase tracking-wider text-muted mb-3">More</h4>
        <ul class="space-y-2 text-sm text-gray-700">
          <li><a href="/about"   class="hover:text-royal hover:underline">About Kusoma</a></li>
          <li><a href="/contact" class="hover:text-royal hover:underline">Contact</a></li>
          <li><a href="/advertise" class="hover:text-royal hover:underline">Advertise</a></li>
          <li><a href="/privacy"   class="hover:text-royal hover:underline">Privacy Policy</a></li>
          <li><a href="/sitemap.xml" class="hover:text-royal hover:underline">Sitemap</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-kborder pt-6 flex flex-wrap items-center justify-between gap-4 text-xs text-muted">
      <p>© {{ date('Y') }} Kusoma. All rights reserved.</p>
      <p>Built with Laravel · Tailwind CSS · Hosted in Africa 🌍</p>
    </div>
  </div>
</footer>
