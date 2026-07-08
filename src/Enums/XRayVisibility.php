<?php

namespace LBHurtado\XRay\Enums;

enum XRayVisibility: string
{
    case Hidden = 'hidden';
    case Visible = 'visible';
    case Redacted = 'redacted';
    case Conditional = 'conditional';
}
