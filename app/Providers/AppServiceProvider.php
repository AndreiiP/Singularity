<?php

namespace App\Providers;

use App\Services\Nasa\NasaClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NasaClient::class, function ($app) {
            $cfg = $app['config']->get('nasa');
            return new NasaClient(
                apiKey:   $cfg['api_key'],
                baseUrl:  $cfg['base_url'],
                timeout:  $cfg['timeout'],
                cacheTtl: $cfg['cache_ttl'],
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
