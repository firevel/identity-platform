<?php

namespace Firevel\IdentityPlatform\Providers;

use Firevel\IdentityPlatform\Services\IdentityPlatformService;
use Firevel\IdentityPlatform\Commands\IdentityPlatformBatchCreate;
use Illuminate\Support\ServiceProvider;

class IdentityPlatformServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/identityplatform.php', 'identityplatform');

        $this->app->singleton(IdentityPlatformService::class, function ($app) {
            $identityPlatform = new IdentityPlatformService(
                config('identityplatform.api'),
                app(\Google\Auth\ApplicationDefaultCredentials::class)->getCredentials()->fetchAuthToken()['access_token']
            );
            if (! empty(config('identityplatform.project_id'))) {
                $identityPlatform->setProject(config('identityplatform.project_id'));
            }
            
            return $identityPlatform;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [IdentityPlatformService::class];
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                IdentityPlatformBatchCreate::class
            ]);
        }

        $this->publishes([
            __DIR__.'/../../config/identityplatform.php' => config_path('identityplatform.php'),
        ], 'config');
    }
}
