<?php

namespace LBHurtado\XRay\Enums;

enum XRayAudience: string
{
    case Guest = 'guest';
    case Onboarded = 'onboarded';
    case Issuer = 'issuer';
    case Admin = 'admin';
    case Auditor = 'auditor';
    case System = 'system';
}
