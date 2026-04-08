<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Models\Post;
use App\Observers\PostObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register the Post observer (auto reading_time, cache busting)
        Post::observe(PostObserver::class);

        // Share category list with every view (used by nav + footer)
        // Cached separately so it doesn't run a query on every render
        View::composer('*', function ($view) {
            $view->with('_navCategories', \Illuminate\Support\Facades\Cache::remember(
                'nav_categories',
                now()->addHours(6),
                fn () => \App\Models\Category::orderBy('name')->get()
            ));
        });
    }
}