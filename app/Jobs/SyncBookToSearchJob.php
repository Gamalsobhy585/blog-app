<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Modules\Book\Services\BookSearchService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncBookToSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;
    public int $backoff = 5;

    public function __construct(public Book $book) {}

    public function handle(BookSearchService $bookSearchService): void
    {
        $bookSearchService->index($this->book);
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('SyncBookToSearchJob permanently failed', [
            'book_id' => $this->book->id,
            'slug'    => $this->book->slug,
            'error'   => $exception->getMessage(),
        ]);
    }
}