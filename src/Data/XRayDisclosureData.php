<?php

namespace LBHurtado\XRay\Data;

use Spatie\LaravelData\Data;

class XRayDisclosureData extends Data
{
    public function __construct(
        public string $key,
        public mixed $value = null,
        public string $visibility = 'visible',
        public ?string $label = null,
        public array $meta = [],
    ) {}
}
