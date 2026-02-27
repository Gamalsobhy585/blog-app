<?php

namespace App\Providers;

use App\Models\Author;
use App\Models\PersonalAccessToken;
use App\Modules\Author\Events\AuthorCreated;
use App\Modules\Author\Events\AuthorPendingApproval;
use App\Modules\Author\Listeners\IndexAuthorInElasticsearch;
use App\Modules\Author\Listeners\SendAuthorApprovalNotification;
use App\Modules\Author\Policies\AuthorPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
                    Schema::defaultStringLength(191);

                    
            RateLimiter::for('auth', function (Request $request) {
                $key = strtolower((string) $request->input('email')) ?: $request->ip();
                return Limit::perMinute(10)->by($key);
            });

            RateLimiter::for('password', function (Request $request) {
                $key = strtolower((string) $request->input('email')) ?: $request->ip();
                return Limit::perMinute(5)->by($key);
            });
            


            Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
            Gate::policy(Author::class, AuthorPolicy::class);

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