<?php

namespace App\Observers;

use App\Jobs\DeleteAuthorFromElasticsearchJob;
use App\Jobs\SyncAuthorToElasticsearchJob;
use App\Models\Author;
use Illuminate\Support\Facades\Cache;

class AuthorObserver
{
    public function created(Author $author): void
    {
        Cache::tags(['authors'])->flush();
        // dispatch job to index in ES
        SyncAuthorToElasticsearchJob::dispatch($author->id);
    }

    public function updated(Author $author): void
    {
        Cache::tags(['authors'])->flush();
        \App\Jobs\SyncAuthorToElasticsearchJob::dispatch($author->id);
    }

    public function deleted(Author $author): void
    {
        Cache::tags(['authors'])->flush();
        DeleteAuthorFromElasticsearchJob::dispatch($author->id);
    }
}