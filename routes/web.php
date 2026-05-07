<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\{
    AuthorController,
    CategoryController,
    HomeController,
    PostController,
    SearchController,
    WelcomeController,
    ProfileController,
    DashboardController,
    CommentController,
};

// Admin Controllers
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    PostController     as AdminPostController,
    CategoryController as AdminCategoryController,
    TagController      as AdminTagController,
    CommentController  as AdminCommentController,
};

// Sitemap
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// News homepage
Route::get('/news', [HomeController::class, 'index'])->name('news.index');

// Single post (SEO-friendly slug)
Route::get('/news/{slug}', [PostController::class, 'show'])
    ->name('post.show')
    ->where('slug', '[a-z0-9\-]+');

// Post a comment or reply (rate-limited: 5 per minute per IP)
Route::post('/news/{slug}/comments', [CommentController::class, 'store'])
    ->name('post.comment')
    ->middleware('throttle:5,1');

// Like a comment — JSON response (rate-limited: 30 per minute per IP)
Route::post('/comments/{comment}/like', [CommentController::class, 'like'])
    ->name('comment.like')
    ->middleware('throttle:30,1');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Author profile
Route::get('/author/{id}', [AuthorController::class, 'show'])
    ->name('author.show')
    ->where('id', '[0-9]+');

// Category archive
Route::get('/category/{slug}', [CategoryController::class, 'show'])
    ->name('category.show')
    ->where('slug', '[a-z0-9\-]+');

// Tag archive
Route::get('/tag/{slug}', [AdminTagController::class, 'show'])
    ->name('tag.show')
    ->where('slug', '[a-z0-9\-]+');



    //dashboard
/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('dashboard')
    ->name('dashboard.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Posts
        Route::resource('posts', AdminPostController::class);

        // Categories
        Route::resource('categories', AdminCategoryController::class);

        // Tags (no individual show page)
        Route::resource('tags', AdminTagController::class)->except(['show']);

        /*
        |----------------------------------------------------------------------
        | Comments moderation
        |
        | IMPORTANT: static-segment routes (approve-all, bulk-*) MUST be
        | registered BEFORE the {comment} wildcard routes so Laravel does not
        | attempt to resolve "approve-all" or "bulk-delete" as a model ID.
        |----------------------------------------------------------------------
        */

        // List all comments
        Route::get('comments', [AdminCommentController::class, 'index'])
            ->name('comments.index');

            
        // Bulk actions — registered before {comment} wildcard
        Route::patch('comments/approve-all', [AdminCommentController::class, 'approveAll'])
            ->name('comments.approveAll');

        Route::post('comments/bulk-approve', [AdminCommentController::class, 'bulkApprove'])
            ->name('comments.bulk-approve');

        Route::delete('comments/bulk-delete', [AdminCommentController::class, 'bulkDelete'])
            ->name('comments.bulk-delete');

        // Single-comment actions — after static routes
        Route::patch('comments/{comment}/approve', [AdminCommentController::class, 'approve'])
            ->name('comments.approve');

        Route::patch('comments/{comment}/disapprove', [AdminCommentController::class, 'disapprove'])
            ->name('comments.disapprove');

        Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])
            ->name('comments.destroy');
    });

    // System Logs
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/logs', [App\Http\Controllers\Admin\LogController::class, 'index'])->name('admin.logs');
});


/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Laravel Breeze / Jetstream)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| SEO ROUTES
|--------------------------------------------------------------------------
*/

// Sitemap (cached for 1 hour)
Route::get('/sitemap.xml', function () {
    return cache()->remember('sitemap_xml', now()->addHour(), function () {

        $sitemap = Sitemap::create()
            ->add(
                Url::create('/')
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(1.0)
            )
            ->add(
                Url::create('/news')
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_HOURLY)
                    ->setPriority(0.9)
            );

        \App\Models\Post::where('status', 'published')
            ->latest('published_at')
            ->each(function ($post) use ($sitemap) {
                $sitemap->add(
                    Url::create("/news/{$post->slug}")
                        ->setLastModificationDate($post->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            });

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


// RSS feed
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


// robots.txt
Route::get('/robots.txt', function () {

    $content = implode("\n", [
        'User-agent: *',
        'Allow: /',
        'Disallow: /dashboard/',
        'Sitemap: ' . url('/sitemap.xml'),
    ]);

    return response($content, 200, ['Content-Type' => 'text/plain']);

})->name('robots');