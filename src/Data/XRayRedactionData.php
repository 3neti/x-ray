<?php

namespace LBHurtado\XRay\Data;

use Spatie\LaravelData\Data;

class XRayRedactionData extends Data
{
    public function __construct(
        public string $key,
        public string $reason = 'policy',
        public ?string $audience = null,
        public array $meta = [],
    ) {}
}
