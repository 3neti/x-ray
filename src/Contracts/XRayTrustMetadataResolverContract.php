<?php

namespace LBHurtado\XRay\Contracts;

use LBHurtado\XRay\Data\XRayTrustMetadataData;

interface XRayTrustMetadataResolverContract
{
    public function resolve(): XRayTrustMetadataData;
}
