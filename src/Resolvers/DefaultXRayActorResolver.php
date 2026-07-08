<?php

namespace LBHurtado\XRay\Resolvers;

use Illuminate\Http\Request;
use LBHurtado\XRay\Contracts\XRayActorResolverContract;
use LBHurtado\XRay\Data\XRayActorData;

class DefaultXRayActorResolver implements XRayActorResolverContract
{
    public function resolve(Request $request): XRayActorData
    {
        $user = $request->user();

        if (! $user) {
            return new XRayActorData(audience: config('x-ray.disclosure.default_audience', 'guest'));
        }

        $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->values()->all() : [];
        $audience = $this->inferAudience($user, $roles);

        return new XRayActorData(
            audience: $audience,
            user_id: method_exists($user, 'getKey') ? $user->getKey() : null,
            guard: method_exists($request, 'route') ? null : null,
            roles: $roles,
            abilities: [],
            authenticated: true,
        );
    }

    protected function inferAudience(mixed $user, array $roles): string
    {
        if (in_array('admin', $roles, true) || (method_exists($user, 'isAdmin') && $user->isAdmin())) {
            return 'admin';
        }

        if (in_array('issuer', $roles, true) || (method_exists($user, 'isIssuer') && $user->isIssuer())) {
            return 'issuer';
        }

        return 'onboarded';
    }
}
