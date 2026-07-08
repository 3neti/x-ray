<?php

namespace LBHurtado\XRay\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use LBHurtado\XRay\Data\XRayResultData;

class XRayResultResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var XRayResultData $result */
        $result = $this->resource;

        return $result->toArray();
    }
}
