<?php

namespace LBHurtado\XRay\Contracts;

use LBHurtado\XRay\Data\XRayContextData;

interface XRayDisclosurePolicyContract
{
    /**
     * @return array{visible: bool, disclosures: array, requirements: array, redactions: array, stages: array, next_actions: array, warnings: array, policy_trace: array}
     */
    public function evaluate(XRayContextData $context, array $projection): array;
}
