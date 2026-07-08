<?php

namespace LBHurtado\XRay\Enums;

enum XRayStatus: string
{
    case Unknown = 'unknown';
    case NotFound = 'not_found';
    case Claimable = 'claimable';
    case Redeemed = 'redeemed';
    case Expired = 'expired';
    case PartiallyClaimable = 'partially_claimable';
    case Hidden = 'hidden';
}
