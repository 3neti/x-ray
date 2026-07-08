<?php

namespace LBHurtado\XRay;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use LBHurtado\XRay\Contracts\XRayActorResolverContract;
use LBHurtado\XRay\Contracts\XRayDisclosurePolicyContract;
use LBHurtado\XRay\Contracts\XRayInspectorContract;
use LBHurtado\XRay\Contracts\XRayTrustMetadataResolverContract;

class XRayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/x-ray.php', 'x-ray');

        $this->app->bind(XRayInspectorContract::class, config('x-ray.services.inspector'));
        $this->app->bind(XRayActorResolverContract::class, config('x-ray.services.actor_resolver'));
        $this->app->bind(XRayDisclosurePolicyContract::class, config('x-ray.services.disclosure_policy'));
        $this->app->bind(XRayTrustMetadataResolverContract::class, config('x-ray.services.trust_metadata_resolver'));
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'x-ray');

        $this->publishes([
            __DIR__.'/../config/x-ray.php' => config_path('x-ray.php'),
        ], 'x-ray-config');

        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js/vendor/x-ray'),
        ], 'x-ray-assets');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/x-ray'),
        ], 'x-ray-views');

        if (config('x-ray.routes.enabled')) {
            Route::middleware(config('x-ray.routes.middleware', ['web']))
                ->prefix(config('x-ray.routes.prefix', 'x-ray'))
                ->group(__DIR__.'/../routes/web.php');
        }
    }
}
