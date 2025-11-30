<?php

use App\Modules\Author\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/authors', [AuthorController::class, 'index'])
        ->name('authors.index')
        ->can('viewAny', 'App\Models\Author');
        
    Route::get('/authors/{author}', [AuthorController::class, 'show'])
        ->name('authors.show')
        ->can('view', 'author');
});

// Librarian and Admin routes
Route::middleware(['auth:sanctum', 'isLibrarian'])->group(function () {
    // Create authors
    Route::post('/authors', [AuthorController::class, 'store'])
        ->name('authors.store')
        ->can('create', 'App\Models\Author');
    
    // Update authors (policy will check if librarian created it)
    Route::put('/authors/{author}', [AuthorController::class, 'update'])
        ->name('authors.update')
        ->can('update', 'author');
        
    Route::patch('/authors/{author}', [AuthorController::class, 'update'])
        ->name('authors.patch')
        ->can('update', 'author');
});

// Admin only routes
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    // Delete authors
    Route::delete('/authors/{author}', [AuthorController::class, 'destroy'])
        ->name('authors.destroy')
        ->can('delete', 'author');
    
    // Approve authors
    Route::post('/authors/{author}/approve', [AuthorController::class, 'approve'])
        ->name('authors.approve')
        ->can('approve', 'author');
});