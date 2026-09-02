<?php

namespace LBHurtado\XRay\Data;

use Spatie\LaravelData\Data;

class XRayResultData extends Data
{
    public function __construct(
        public bool $visible,
        public string $status,
        public string $audience,
        public array $disclosures = [],
        public array $requirements = [],
        public array $redactions = [],
        public array $stages = [],
        public ?XRayTrustMetadataData $trust = null,
        public array $next_actions = [],
        public array $presentation = [],
        public array $warnings = [],
        public array $policy_trace = [],
    ) {}
}
