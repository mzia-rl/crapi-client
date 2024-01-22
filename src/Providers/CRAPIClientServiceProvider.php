<?php

namespace Canzell\Providers;

use Illuminate\Support\ServiceProvider;
use Canzell\Http\Clients\CRAPIClient;

class CRAPIClientServiceProvider extends ServiceProvider
{

    public $singletons = [
        CRAPIClient::class => CRAPIClient::class 
    ];

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/crapi-client.php' => config_path('crapi-client.php')
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../../config/crapi-client.php', 'crapi-client'
        );
    }

}