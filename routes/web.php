<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

// ─── Root landing page (/) — sits BEFORE /news ───────────────────────────────

Route::get('/', [WelcomeController::class, 'index'])->name('home');

// ─── Search ──────────────────────────────────────────────────────────────────

Route::get('/search', [SearchController::class, 'index'])->name('search');

// ─── Author profiles ─────────────────────────────────────────────────────────

Route::get('/author/{id}', [AuthorController::class, 'show'])
     ->name('author.show')
     ->where('id', '[0-9]+');

// ─── Main news routes ────────────────────────────────────────────────────────

Route::get('/news', [HomeController::class, 'index'])->name('news.index');

Route::get('/news/{slug}', [PostController::class, 'show'])
     ->name('post.show')
     ->where('slug', '[a-z0-9\-]+');

Route::post('/news/{slug}/comments', [PostController::class, 'storeComment'])
     ->name('post.comment')
     ->middleware('throttle:5,1');  // 5 comments per minute per IP

Route::get('/category/{slug}', [CategoryController::class, 'show'])
     ->name('category.show')
     ->where('slug', '[a-z0-9\-]+');

Route::get('/tag/{slug}', [TagController::class, 'show'])
     ->name('tag.show')
     ->where('slug', '[a-z0-9\-]+');

// ─── Admin routes ────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
         ->name('dashboard');

    // Posts
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);

    // Categories
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

    // Tags
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)
         ->only(['index', 'store', 'destroy']);

    // Comments
    Route::get('comments', [\App\Http\Controllers\Admin\CommentController::class, 'index'])
         ->name('comments.index');
    Route::patch('comments/{comment}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])
         ->name('comments.approve');
    Route::patch('comments/approve-all', [\App\Http\Controllers\Admin\CommentController::class, 'approveAll'])
         ->name('comments.approveAll');
    Route::delete('comments/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])
         ->name('comments.destroy');
});

// ─── SEO: XML Sitemap (cached, auto-regenerated hourly) ──────────────────────

Route::get('/sitemap.xml', function () {
    return cache()->remember('sitemap_xml', now()->addHour(), function () {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0))
            ->add(Url::create('/news')
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_HOURLY)
                ->setPriority(0.9));

        // All published posts
        \App\Models\Post::where('status', 'published')
            ->orderByDesc('published_at')
            ->each(function ($post) use ($sitemap) {
                $sitemap->add(
                    Url::create("/news/{$post->slug}")
                       ->setLastModificationDate($post->updated_at)
                       ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                       ->setPriority(0.8)
                );
            });

        // Category pages
        \App\Models\Category::all()->each(function ($cat) use ($sitemap) {
            $sitemap->add(
                Url::create("/category/{$cat->slug}")
                   ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                   ->setPriority(0.6)
            );
        });

        return $sitemap->toResponse(request());
    });
})->name('sitemap');

// ─── SEO: News RSS Feed ──────────────────────────────────────────────────────

Route::get('/feed.xml', function () {
    $posts = \App\Models\Post::published()
        ->with(['author', 'category'])
        ->latest()
        ->take(20)
        ->get();

    return response()
        ->view('feeds.rss', compact('posts'))
        ->header('Content-Type', 'application/rss+xml; charset=utf-8');
})->name('feed.rss');

// ─── robots.txt ──────────────────────────────────────────────────────────────

Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nAllow: /\nDisallow: /admin/\nSitemap: " . url('/sitemap.xml') . "\n",
        200,
        ['Content-Type' => 'text/plain']
    );
})->name('robots');