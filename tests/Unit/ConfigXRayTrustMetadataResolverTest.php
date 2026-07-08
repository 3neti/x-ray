<?php

use LBHurtado\XRay\Resolvers\ConfigXRayTrustMetadataResolver;

it('resolves trust metadata without exposing private key', function () {
    config()->set('x-ray.trust.version', '1.2.3');
    config()->set('x-ray.trust.private_key', 'SECRET-PRIVATE-KEY');
    config()->set('x-ray.trust.public_key', 'PUBLIC-KEY');

    $data = app(ConfigXRayTrustMetadataResolver::class)->resolve();

    expect($data->version)->toBe('1.2.3')
        ->and(json_encode($data))->not->toContain('SECRET-PRIVATE-KEY')
        ->and($data->public_key_fingerprint)->toStartWith('sha256:');
});
