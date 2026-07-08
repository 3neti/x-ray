<?php

namespace LBHurtado\XRay\Data;

use Spatie\LaravelData\Data;

class XRayActorData extends Data
{
    public function __construct(
        public string $audience = 'guest',
        public ?int $user_id = null,
        public ?string $guard = null,
        public array $roles = [],
        public array $abilities = [],
        public bool $authenticated = false,
    ) {}
}
