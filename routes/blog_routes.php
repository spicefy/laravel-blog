<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Blog\HomeController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\Blog\CategoryController;
use App\Http\Controllers\Blog\PostController;
use App\Http\Controllers\Blog\SearchController;
use App\Http\Controllers\Blog\CommentController;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/*
|--------------------------------------------------------------------------
| BLOG PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Blog homepage
Route::get('/news', [HomeController::class, 'index'])
    ->name('news.index');

// Single post
Route::get('/news/{slug}', [PostController::class, 'show'])
    ->name('post.show')
    ->where('slug', '[a-z0-9\-]+');

// Post comment
Route::post('/news/{slug}/comments', [CommentController::class, 'store'])
    ->name('post.comment')
    ->middleware('throttle:5,1');

// Like comment
Route::post('/comments/{comment}/like', [CommentController::class, 'like'])
    ->name('comment.like')
    ->middleware('throttle:30,1');

// Search
Route::get('/search', [SearchController::class, 'index'])
    ->name('search');

// Author
Route::get('/author/{id}', [AuthorController::class, 'show'])
    ->name('author.show')
    ->where('id', '[0-9]+');

// Category
Route::get('/category/{slug}', [CategoryController::class, 'show'])
    ->name('category.show')
    ->where('slug', '[a-z0-9\-]+');

// Tag
Route::get('/tag/{slug}', [\App\Http\Controllers\Admin\TagController::class, 'show'])
    ->name('tag.show')
    ->where('slug', '[a-z0-9\-]+');


/*
|--------------------------------------------------------------------------
| SITEMAP
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', function () {

    return cache()->remember(
        'sitemap_xml',
        now()->addHour(),
        function () {

            $sitemap = Sitemap::create()

                // Homepage
                ->add(
                    Url::create(url('/'))
                        ->setChangeFrequency(
                            Url::CHANGE_FREQUENCY_DAILY
                        )
                        ->setPriority(1.0)
                )

                // News homepage
                ->add(
                    Url::create(url('/news'))
                        ->setChangeFrequency(
                            Url::CHANGE_FREQUENCY_HOURLY
                        )
                        ->setPriority(0.9)
                );

            /*
            |--------------------------------------------------------------------------
            | Posts
            |--------------------------------------------------------------------------
            */

            \App\Models\Post::published()
                ->latest('published_at')
                ->each(function ($post) use ($sitemap) {

                    $sitemap->add(
                        Url::create(
                            url("/news/{$post->slug}")
                        )
                            ->setLastModificationDate($post->updated_at)
                            ->setChangeFrequency(
                                Url::CHANGE_FREQUENCY_WEEKLY
                            )
                            ->setPriority(0.8)
                    );
                });

            /*
            |--------------------------------------------------------------------------
            | Categories
            |--------------------------------------------------------------------------
            */

            \App\Models\Category::where('is_active', true)
                ->each(function ($category) use ($sitemap) {

                    $sitemap->add(
                        Url::create(
                            url("/category/{$category->slug}")
                        )
                            ->setChangeFrequency(
                                Url::CHANGE_FREQUENCY_DAILY
                            )
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
        [
            'Content-Type' => 'text/plain',
        ]
    );

})->name('robots');