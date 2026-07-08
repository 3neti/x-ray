<?php

namespace LBHurtado\XRay\Contracts;

use Illuminate\Http\Request;
use LBHurtado\XRay\Data\XRayActorData;

interface XRayActorResolverContract
{
    public function resolve(Request $request): XRayActorData;
}
