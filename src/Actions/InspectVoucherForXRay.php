<?php

namespace LBHurtado\XRay\Actions;

use Illuminate\Support\Arr;
use LBHurtado\XRay\Contracts\XRayDisclosurePolicyContract;
use LBHurtado\XRay\Contracts\XRayInspectorContract;
use LBHurtado\XRay\Contracts\XRayTrustMetadataResolverContract;
use LBHurtado\XRay\Data\XRayContextData;
use LBHurtado\XRay\Data\XRayResultData;
use LBHurtado\XRay\Support\NormalizesDisclosurePayload;

class InspectVoucherForXRay implements XRayInspectorContract
{
    use NormalizesDisclosurePayload;

    public function __construct(
        protected XRayDisclosurePolicyContract $policy,
        protected XRayTrustMetadataResolverContract $trust,
    ) {}

    public function handle(XRayContextData $context, mixed $voucherProjection = null): XRayResultData
    {
        $projection = $this->projectionToArray($voucherProjection);
        $decision = $this->policy->evaluate($context, $projection);

        return new XRayResultData(
            visible: (bool) Arr::get($decision, 'visible', false),
            status: (string) Arr::get($projection, 'status', 'unknown'),
            audience: $context->actor->audience,
            disclosures: Arr::get($decision, 'disclosures', []),
            requirements: Arr::get($decision, 'requirements', []),
            redactions: Arr::get($decision, 'redactions', []),
            stages: Arr::get($decision, 'stages', []),
            trust: $this->trust->resolve(),
            next_actions: Arr::get($decision, 'next_actions', []),
            warnings: Arr::get($decision, 'warnings', []),
            policy_trace: Arr::get($decision, 'policy_trace', []),
        );
    }
}
