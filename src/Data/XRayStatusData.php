<?php

namespace LBHurtado\XRay\Data;

use Spatie\LaravelData\Data;

class XRayStatusData extends Data
{
    public function __construct(
        public string $value = 'unknown',
        public ?string $label = null,
        public ?string $description = null,
        public array $meta = [],
    ) {}
}
