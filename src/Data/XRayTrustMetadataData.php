<?php

namespace LBHurtado\XRay\Data;

use Spatie\LaravelData\Data;

class XRayTrustMetadataData extends Data
{
    public function __construct(
        public ?string $version = null,
        public ?string $copyright = null,
        public array $licenses = [],
        public ?string $widget_url = null,
        public bool $signatures_enabled = false,
        public ?string $public_key_fingerprint = null,
    ) {}
}
