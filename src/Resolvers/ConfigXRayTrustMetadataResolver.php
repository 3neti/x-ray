<?php

namespace LBHurtado\XRay\Resolvers;

use LBHurtado\XRay\Contracts\XRayTrustMetadataResolverContract;
use LBHurtado\XRay\Data\XRayTrustMetadataData;
use LBHurtado\XRay\Support\MasksSensitiveValues;

class ConfigXRayTrustMetadataResolver implements XRayTrustMetadataResolverContract
{
    use MasksSensitiveValues;

    public function resolve(): XRayTrustMetadataData
    {
        $publicKey = config('x-ray.trust.public_key');

        return new XRayTrustMetadataData(
            version: config('x-ray.trust.version'),
            copyright: config('x-ray.trust.copyright'),
            licenses: array_filter(config('x-ray.trust.licenses', [])),
            widget_url: config('x-ray.trust.widget_url'),
            signatures_enabled: filter_var(config('x-ray.trust.signatures_enabled'), FILTER_VALIDATE_BOOL),
            public_key_fingerprint: config('x-ray.masking.public_key_as_fingerprint', true)
                ? $this->fingerprint($publicKey)
                : $publicKey,
        );
    }
}
