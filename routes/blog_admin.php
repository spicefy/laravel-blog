<?php

use Illuminate\Support\Facades\Route;

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
| BLOG ADMIN ROUTES
|--------------------------------------------------------------------------
|
| URL:
| /dashboard/...
|
| Names:
| admin....
|
*/

Route::prefix('dashboard')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
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

        Route::get('comments', [AdminCommentController::class, 'index'])
            ->name('comments.index');

        /*
        | Static routes MUST come before {comment}
        */

        Route::patch(
            'comments/approve-all',
            [AdminCommentController::class, 'approveAll']
        )->name('comments.approveAll');

        Route::post(
            'comments/bulk-approve',
            [AdminCommentController::class, 'bulkApprove']
        )->name('comments.bulk-approve');

        Route::delete(
            'comments/bulk-delete',
            [AdminCommentController::class, 'bulkDelete']
        )->name('comments.bulk-delete');

        /*
        | Individual comments
        */

        Route::patch(
            'comments/{comment}/approve',
            [AdminCommentController::class, 'approve']
        )->name('comments.approve');

        Route::patch(
            'comments/{comment}/disapprove',
            [AdminCommentController::class, 'disapprove']
        )->name('comments.disapprove');

        Route::delete(
            'comments/{comment}',
            [AdminCommentController::class, 'destroy']
        )->name('comments.destroy');


        /*
        |--------------------------------------------------------------------------
        | System Logs
        |--------------------------------------------------------------------------
        */

        Route::get('logs', [LogController::class, 'index'])
            ->name('logs');
    });