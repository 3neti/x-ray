<?php

namespace LBHurtado\XRay\Contracts;

use LBHurtado\XRay\Data\XRayContextData;
use LBHurtado\XRay\Data\XRayResultData;

interface XRayInspectorContract
{
    public function handle(XRayContextData $context, mixed $voucherProjection = null): XRayResultData;
}
