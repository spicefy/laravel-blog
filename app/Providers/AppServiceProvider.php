<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Category;
use App\Observers\PostObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register observer
        Post::observe(PostObserver::class);

        // Share navigation categories globally
        View::composer('*', function ($view) {

            $categories = Cache::remember('nav_categories', now()->addHours(6), function () {
                return Category::orderBy('name')->get();
            });

            $view->with('_navCategories', $categories);
        });
    }
}