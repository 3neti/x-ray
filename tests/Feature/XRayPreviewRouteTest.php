<?php

it('renders the default x-ray view', function () {
    $this->get('/x-ray')
        ->assertOk()
        ->assertSee('x-ray UI assets are installed');
});

it('ships its standalone package resources', function () {
    $packageRoot = dirname(__DIR__, 2);

    expect($packageRoot.'/config/x-ray.php')->toBeFile()
        ->and($packageRoot.'/routes/web.php')->toBeFile()
        ->and($packageRoot.'/resources/views/x-ray.blade.php')->toBeFile()
        ->and(view()->exists('x-ray::x-ray'))->toBeTrue()
        ->and(config('x-ray.routes.enabled'))->toBeTrue();
});
