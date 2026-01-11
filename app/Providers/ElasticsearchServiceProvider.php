<?php

namespace App\Providers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            $builder = ClientBuilder::create()->setElasticMetaHeader(false);

            // Host-based setup
            if (config('elasticsearch.cloud_id')) {
                // Elastic Cloud
                $builder->setElasticCloudId(config('elasticsearch.cloud_id'));

                if (config('elasticsearch.api_key')) {
                    $builder->setApiKey(config('elasticsearch.api_key'));
                }
            } else {
                // Self-hosted
                $builder->setHosts([config('elasticsearch.host')]);

                if (config('elasticsearch.username') && config('elasticsearch.password')) {
                    $builder->setBasicAuthentication(
                        config('elasticsearch.username'),
                        config('elasticsearch.password')
                    );
                }
            }

            // SSL verification
            $builder->setSSLVerification(config('elasticsearch.ssl_verification'));

            return $builder->build();
        });
    }
}
