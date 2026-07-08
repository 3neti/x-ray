<?php

namespace LBHurtado\XRay\Support;

trait NormalizesDisclosurePayload
{
    public function projectionToArray(mixed $projection): array
    {
        if ($projection === null) {
            return [];
        }

        if (is_array($projection)) {
            return $projection;
        }

        if (method_exists($projection, 'toArray')) {
            return $projection->toArray();
        }

        return json_decode(json_encode($projection, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }
}
