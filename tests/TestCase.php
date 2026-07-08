<?php

// tests/TestCase.php

namespace LBHurtado\XRay\Tests;

use LBHurtado\XRay\XRayServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            XRayServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        $app['config']->set('x-ray.trust.private_key', 'TEST-PRIVATE-KEY');
        $app['config']->set('x-ray.trust.public_key', 'TEST-PUBLIC-KEY');
    }
}
