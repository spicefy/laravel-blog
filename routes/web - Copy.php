<?php

use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

// Public Controllers
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Blog\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommentController;

// Admin Controllers
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    PostController as AdminPostController,
    CategoryController as AdminCategoryController,
    TagController as AdminTagController,
    CommentController as AdminCommentController,
    LogController
};

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [WelcomeController::class, 'index'])
    ->name('home');

// News homepage
Route::get('/news', [HomeController::class, 'index'])
    ->name('news.index');

// Single post
Route::get('/news/{slug}', [PostController::class, 'show'])
    ->name('post.show')
    ->where('slug', '[a-z0-9\-]+');

// Post a comment or reply
Route::post('/news/{slug}/comments', [CommentController::class, 'store'])
    ->name('post.comment')
    ->middleware('throttle:5,1');

// Like a comment
Route::post('/comments/{comment}/like', [CommentController::class, 'like'])
    ->name('comment.like')
    ->middleware('throttle:30,1');

// Search
Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

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


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // User dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
|
| URLs remain /dashboard/...
| Route names use admin.* to avoid conflicts with the user dashboard.
|
*/

Route::prefix('dashboard')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Admin Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Posts
        |--------------------------------------------------------------------------
        */

        Route::resource('posts', AdminPostController::class);


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        Route::resource('categories', AdminCategoryController::class);


        /*
        |--------------------------------------------------------------------------
        | Tags
        |--------------------------------------------------------------------------
        */

        Route::resource('tags', AdminTagController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Comments
        |--------------------------------------------------------------------------
        */

        // List comments
        Route::get('comments', [AdminCommentController::class, 'index'])
            ->name('comments.index');

        // Bulk actions MUST come before {comment}
        Route::patch('comments/approve-all', [AdminCommentController::class, 'approveAll'])
            ->name('comments.approveAll');

        Route::post('comments/bulk-approve', [AdminCommentController::class, 'bulkApprove'])
            ->name('comments.bulk-approve');

        Route::delete('comments/bulk-delete', [AdminCommentController::class, 'bulkDelete'])
            ->name('comments.bulk-delete');

        // Individual comment actions
        Route::patch('comments/{comment}/approve', [AdminCommentController::class, 'approve'])
            ->name('comments.approve');

        Route::patch('comments/{comment}/disapprove', [AdminCommentController::class, 'disapprove'])
            ->name('comments.disapprove');

        Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])
            ->name('comments.destroy');


        /*
        |--------------------------------------------------------------------------
        | System Logs
        |--------------------------------------------------------------------------
        */

        Route::get('logs', [LogController::class, 'index'])
            ->name('logs');
    });


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| SEO ROUTES
|--------------------------------------------------------------------------
*/

// Sitemap
Route::get('/sitemap.xml', function () {

    return cache()->remember(
        'sitemap_xml',
        now()->addHour(),
        function () {

            $sitemap = Sitemap::create()
                ->add(
                    Url::create(url('/'))
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                        ->setPriority(1.0)
                )
                ->add(
                    Url::create(url('/news'))
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_HOURLY)
                        ->setPriority(0.9)
                );

            // Published posts
            \App\Models\Post::published()
                ->latest('published_at')
                ->each(function ($post) use ($sitemap) {

                    $sitemap->add(
                        Url::create(url("/news/{$post->slug}"))
                            ->setLastModificationDate($post->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.8)
                    );
                });

            // Categories
            \App\Models\Category::where('is_active', true)
                ->each(function ($category) use ($sitemap) {

                    $sitemap->add(
                        Url::create(url("/category/{$category->slug}"))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                            ->setPriority(0.6)
                    );
                });

            return $sitemap->toResponse(request());
        }
    );

})->name('sitemap');


/*
|--------------------------------------------------------------------------
| RSS FEED
|--------------------------------------------------------------------------
*/

Route::get('/feed.xml', function () {

    $posts = \App\Models\Post::published()
        ->with(['author', 'category'])
        ->latest('published_at')
        ->take(20)
        ->get();

    return response()
        ->view('feeds.rss', compact('posts'))
        ->header(
            'Content-Type',
            'application/rss+xml; charset=utf-8'
        );

})->name('feed.rss');


/*
|--------------------------------------------------------------------------
| ROBOTS.TXT
|--------------------------------------------------------------------------
*/

Route::get('/robots.txt', function () {

    $content = implode("\n", [
        'User-agent: *',
        'Allow: /',
        'Disallow: /dashboard/',
        'Sitemap: ' . url('/sitemap.xml'),
    ]);

    return response(
        $content,
        200,
        ['Content-Type' => 'text/plain']
    );

})->name('robots');