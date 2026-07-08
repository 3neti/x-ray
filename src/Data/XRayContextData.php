<?php

namespace LBHurtado\XRay\Data;

use Spatie\LaravelData\Data;

class XRayContextData extends Data
{
    public function __construct(
        public string $code,
        public XRayActorData $actor,
        public string $channel = 'claim',
        public array $request = [],
        public array $meta = [],
    ) {}
}
