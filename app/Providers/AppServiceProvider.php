<?php

namespace App\Providers;

use App\Models\Author;
use App\Models\Book;
use App\Models\PersonalAccessToken;
use App\Modules\Author\Events\AuthorCreated;
use App\Modules\Author\Events\AuthorPendingApproval;
use App\Modules\Author\Listeners\IndexAuthorInElasticsearch;
use App\Modules\Author\Listeners\SendAuthorApprovalNotification;
use App\Observers\AuthorObserver;
use App\Observers\BookObserver;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
// AppServiceProvider.php
$this->app->singleton(Client::class, function () {
    return ClientBuilder::create()
        ->setHosts([config('elasticsearch.host', 'http://localhost:9200')])
        ->setHttpClientOptions([
            'timeout' => 10,
            'connect_timeout' => 5,
        ])
        ->build();
});
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Author::observe(AuthorObserver::class);
        Book::observe(BookObserver::class);

        RateLimiter::for('auth', function (Request $request) {
            $key = strtolower((string) $request->input('email')) ?: $request->ip();
            return Limit::perMinute(10)->by($key);
        });

        RateLimiter::for('password', function (Request $request) {
            $key = strtolower((string) $request->input('email')) ?: $request->ip();
            return Limit::perMinute(5)->by($key);
        });

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Event::listen(
            AuthorPendingApproval::class,
            SendAuthorApprovalNotification::class,
        );

        Event::listen(
            AuthorCreated::class,
            IndexAuthorInElasticsearch::class,
        );
    }
}