<?php

use LBHurtado\XRay\Actions\InspectVoucherForXRay;
use LBHurtado\XRay\Policies\DefaultXRayDisclosurePolicy;
use LBHurtado\XRay\Resolvers\ConfigXRayTrustMetadataResolver;
use LBHurtado\XRay\Resolvers\DefaultXRayActorResolver;

return [
    'enabled' => env('XRAY_ENABLED', true),

    'routes' => [
        'enabled' => env('XRAY_ROUTES_ENABLED', true),
        'prefix' => env('XRAY_ROUTE_PREFIX', 'x-ray'),
        'middleware' => ['web'],
    ],

    'api' => [
        'enabled' => env('XRAY_API_ENABLED', true),
        'prefix' => env('XRAY_API_PREFIX', 'api/x-ray/v1'),
        'middleware' => ['api'],
    ],

    'ui' => [
        'enabled' => env('XRAY_UI_ENABLED', true),
        'publishable' => true,
        'default_component' => 'XRayWidget',
    ],

    'trust' => [
        'version' => env('VOUCHER_VERSION', '1.0.0'),
        'copyright' => env('VOUCHER_COPYRIGHT', '3neti R&D OPC'),
        'licenses' => [
            'bsp' => env('LICENSE_BSP', 'Bangko Sentral ng Pilipinas'),
            'sec' => env('LICENSE_SEC', 'Securities and Exchange Commission'),
            'ntc' => env('LICENSE_NTC', 'National Telecommunications Commission'),
        ],
        'widget_url' => env('VOUCHER_WIDGET_URL'),
        'signatures_enabled' => env('VOUCHER_ENABLE_SIGNATURES', false),
        'public_key' => env('VOUCHER_PUBLIC_KEY'),
        'private_key' => env('VOUCHER_PRIVATE_KEY'),
    ],

    'disclosure' => [
        'default_audience' => 'guest',

        'guest' => [
            'show_exists' => true,
            'show_status' => true,
            'show_amount' => false,
            'show_issuer' => false,
            'show_requirements' => true,
            'show_rider_preclaim' => false,
            'show_remaining_slices' => 'if_allowed_by_voucher',
            'show_redirect_url' => false,
            'show_policy_trace' => false,
        ],

        'onboarded' => [
            'show_exists' => true,
            'show_status' => true,
            'show_amount' => 'if_allowed_by_voucher',
            'show_issuer' => 'if_allowed_by_voucher',
            'show_requirements' => true,
            'show_rider_preclaim' => 'if_allowed_by_voucher',
            'show_remaining_slices' => 'if_allowed_by_voucher',
            'show_redirect_url' => false,
            'show_policy_trace' => false,
        ],

        'issuer' => [
            'show_exists' => true,
            'show_status' => true,
            'show_amount' => true,
            'show_issuer' => true,
            'show_requirements' => true,
            'show_rider_preclaim' => true,
            'show_remaining_slices' => true,
            'show_redirect_url' => true,
            'show_policy_trace' => false,
        ],

        'admin' => [
            'show_exists' => true,
            'show_status' => true,
            'show_amount' => true,
            'show_issuer' => true,
            'show_requirements' => true,
            'show_rider_preclaim' => true,
            'show_remaining_slices' => true,
            'show_redirect_url' => true,
            'show_policy_trace' => true,
        ],
    ],

    'masking' => [
        'mobile' => true,
        'email' => true,
        'account_number' => true,
        'public_key_as_fingerprint' => true,
        'never_expose_private_key' => true,
    ],

    'services' => [
        'inspector' => InspectVoucherForXRay::class,
        'actor_resolver' => DefaultXRayActorResolver::class,
        'trust_metadata_resolver' => ConfigXRayTrustMetadataResolver::class,
        'disclosure_policy' => DefaultXRayDisclosurePolicy::class,
    ],
];
