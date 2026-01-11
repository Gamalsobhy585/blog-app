<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use App\Models\Author;
use App\Modules\Author\Policies\AuthorPolicy;
use App\Modules\Author\Events\AuthorPendingApproval;
use App\Modules\Author\Events\AuthorCreated;
use App\Modules\Author\Listeners\SendAuthorApprovalNotification;
use App\Modules\Author\Listeners\IndexAuthorInElasticsearch;
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
