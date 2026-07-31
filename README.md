# x-ray

`3neti/x-ray` provides permissioned, pre-claim inspection and disclosure for Pay Codes.

It resolves the current actor, applies an explicit disclosure policy, masks sensitive
values, and renders a host-extensible inspection surface. It does not claim, redeem,
execute, or mutate a Pay Code.

## Requirements

- PHP 8.3 or 8.4
- Laravel 12 or 13

## Installation

```bash
composer require 3neti/x-ray
php artisan vendor:publish --tag=x-ray-config
```

Laravel discovers `LBHurtado\XRay\XRayServiceProvider` automatically. The package
loads its configuration, namespaced view, and optional route from the package itself.
Published assets and views are host overrides, not required source files.

The default preview route is controlled by `x-ray.routes.enabled`, prefix, and
middleware configuration. Production deployments should place it behind the
application's authenticated, authorized disclosure boundary.

## Security boundary

- Disclosure is policy-driven and denied unless the configured policy permits it.
- Sensitive values are masked before presentation.
- Host applications may replace actor, trust-metadata, disclosure-policy, and
  inspector implementations through the documented service bindings.
- x-ray is read-only. Claim execution remains the responsibility of x-change.

## Quality gates

```bash
composer validate --strict
composer pint
composer test
composer audit
```

The hosted test matrix covers PHP 8.3 with Laravel 12 and PHP 8.4 with Laravel 13.
