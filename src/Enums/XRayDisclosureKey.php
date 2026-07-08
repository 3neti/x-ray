<?php

namespace LBHurtado\XRay\Enums;

enum XRayDisclosureKey: string
{
    case Exists = 'exists';
    case Status = 'status';
    case Amount = 'amount';
    case Issuer = 'issuer';
    case Requirements = 'requirements';
    case RiderPreClaim = 'rider_preclaim';
    case RemainingSlices = 'remaining_slices';
    case RedirectUrl = 'redirect_url';
}
