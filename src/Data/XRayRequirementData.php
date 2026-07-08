<?php

namespace LBHurtado\XRay\Data;

use Spatie\LaravelData\Data;

class XRayRequirementData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        public bool $required = true,
        public ?string $description = null,
        public array $meta = [],
    ) {}
}
