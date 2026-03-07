<?php

namespace App\Observers;

use App\Jobs\DeleteBookFromElasticsearchJob;
use App\Jobs\SyncBookToSearchJob;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookObserver
{
    public function created(Book $book): void
    {
        Cache::tags(['books'])->flush();
        // dispatch job to index in ES
        SyncBookToSearchJob::dispatch($book);
    }

    public function updated(Book $book): void
    {
        Cache::tags(['books'])->flush();
        \App\Jobs\SyncBookToSearchJob::dispatch($book);
    }

    public function deleted(Book $book): void
    {
        Cache::tags(['books'])->flush();
        DeleteBookFromElasticsearchJob::dispatch($book);
    }
}