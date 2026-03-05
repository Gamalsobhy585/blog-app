<?php

use App\Modules\Author\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // ─── Public (authenticated) ───────────────────────────────────────────────
    Route::get('/authors', [AuthorController::class, 'index'])
        ->name('authors.index')
        ->middleware('permission:authors.view');

    // ─── Admin & Librarian ────────────────────────────────────────────────────
    Route::middleware('role:admin|librarian')->group(function () {
        Route::post('/authors', [AuthorController::class, 'store'])
            ->name('authors.store');

        Route::post('/authors/import', [AuthorController::class, 'import'])
            ->name('authors.import');
    });

    // ─── Admin only ───────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::post('/authors/{author:uuid}/approve', [AuthorController::class, 'approve'])
            ->name('authors.approve');

        Route::post('/authors/{author:uuid}/reject', [AuthorController::class, 'reject'])
            ->name('authors.reject');
    });
});