<?php

use App\Modules\Book\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // ─── Public (authenticated) ───────────────────────────────────────────────
    Route::get('/books', [BookController::class, 'index'])
        ->name('books.index')
        ->middleware('permission:books.view');

    // ─── Admin & Librarian ────────────────────────────────────────────────────
    Route::middleware('role:admin|librarian')->group(function () {

        Route::post('/books', [BookController::class, 'store'])
            ->name('books.store');

        Route::post('/books/import', [BookController::class, 'import'])
            ->name('books.import');

        Route::get('/books/{book}', [BookController::class, 'show'])
        ->name('books.show');
    });

    // ─── Admin only ───────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        Route::post('/books/{book}/approve', [BookController::class, 'approve'])
            ->name('books.approve');

        Route::post('/books/{book}/reject', [BookController::class, 'reject'])
            ->name('books.reject');
    });
});