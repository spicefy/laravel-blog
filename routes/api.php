<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are automatically prefixed with /api and wrapped in the
| 'api' middleware group (stateless, throttled) by RouteServiceProvider.
|
| Full URLs:
|   GET /api/v1/news
|   GET /api/v1/news/{slug}
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('/news', [PostApiController::class, 'index']);
    Route::get('/news/{slug}', [PostApiController::class, 'show'])
        ->where('slug', '[a-z0-9\-]+');
});