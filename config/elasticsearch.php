<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Host
    |--------------------------------------------------------------------------
    |
    | The host and port of your Elasticsearch server
    |
    */
    'host' => env('ELASTICSEARCH_HOST', 'localhost:9200'),

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Elasticsearch authentication credentials
    |
    */
    'username' => env('ELASTICSEARCH_USERNAME'),
    'password' => env('ELASTICSEARCH_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Cloud Configuration
    |--------------------------------------------------------------------------
    |
    | For Elasticsearch Cloud/Elastic Cloud
    |
    */
    'cloud_id' => env('ELASTICSEARCH_CLOUD_ID'),
    'api_key' => env('ELASTICSEARCH_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Enable/disable SSL certificate verification
    |
    */
    'ssl_verification' => env('ELASTICSEARCH_SSL_VERIFICATION', true),

    /*
    |--------------------------------------------------------------------------
    | Index Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for Elasticsearch indices
    |
    */
    'index' => [
        'authors' => 'authors',
    ],
];