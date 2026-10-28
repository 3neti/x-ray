<?php

use LBHurtado\XRay\Data\XRayActorData;
use LBHurtado\XRay\Data\XRayContextData;
use LBHurtado\XRay\Policies\DefaultXRayDisclosurePolicy;

it('redacts guest amount and redirect URL by default', function () {
    $policy = app(DefaultXRayDisclosurePolicy::class);
    $context = new XRayContextData(
        code: 'ABC123',
        actor: new XRayActorData(audience: 'guest'),
    );

    $decision = $policy->evaluate($context, [
        'status' => 'claimable',
        'amount' => '1000.00',
        'redirect_url' => 'https://example.com/secret',
        'requirements' => [
            ['key' => 'mobile', 'label' => 'Mobile number'],
        ],
    ]);

    $redactionKeys = collect($decision['redactions'])->pluck('key')->all();

    expect($decision['disclosures'])->sequence(
        fn ($item) => $item->key->toBe('status'),
    )
        ->and($redactionKeys)->toContain('amount', 'redirect_url')
        ->and($decision['requirements'])->toHaveCount(1);
});

it('discloses only explicitly allowed public Pay Code details', function () {
    $policy = app(DefaultXRayDisclosurePolicy::class);
    $context = new XRayContextData(code: 'JK33', actor: new XRayActorData(audience: 'guest'));

    $decision = $policy->evaluate($context, [
        'status' => 'claimable',
        'amount' => '₱50.00',
        'issuer' => 'Amelia',
        'funding_source' => 'Issuer-funded Pay Code reserve',
        'provider' => 'NetBank',
        'reference' => 'JK33',
        'issued_at' => '2026-10-28T08:33:00+08:00',
        'expires_at' => '2026-10-29T08:33:00+08:00',
        'redirect_url' => 'https://example.com/private',
        'service_info' => [
            'host_name' => 'x-change',
            'x_change_version' => 'v1.1.2',
            'x_ray_version' => 'v1.0.3',
            'publisher' => '3neti',
            'authors' => ['Ada', ' Anaïs ', null, str_repeat('A', 121)],
            'contributors' => ['Ada'],
            'private_key' => 'never-public',
        ],
        'allow' => [
            'amount' => true,
            'issuer' => true,
            'funding_source' => true,
            'provider' => true,
            'reference' => true,
            'issued_at' => true,
            'expires_at' => true,
            'service_info' => true,
        ],
    ]);

    $disclosures = collect($decision['disclosures'])->keyBy('key');

    expect($disclosures->pluck('key')->all())->toContain('amount', 'issuer', 'provider', 'reference', 'expires_at')
        ->and($disclosures->get('service_info')['value'])->not->toHaveKey('private_key')
        ->and($disclosures->get('service_info')['value']['x_ray_version'])->toBe('v1.0.3')
        ->and($disclosures->get('service_info')['value']['authors'])->toBe(['Ada', 'Anaïs', str_repeat('A', 120)])
        ->and(collect($decision['redactions'])->pluck('key')->all())->toContain('redirect_url');
});

it('discloses sanitized slice rows to guests only when the voucher allows it', function () {
    $policy = app(DefaultXRayDisclosurePolicy::class);
    $context = new XRayContextData(
        code: 'SLICED',
        actor: new XRayActorData(audience: 'guest'),
    );
    $rows = [[
        'id' => 'slice_1',
        'label' => 'Slice 1',
        'amount_minor' => 2_500,
        'status' => 'available',
        'status_label' => 'Available',
    ]];

    $allowed = $policy->evaluate($context, [
        'status' => 'claimable',
        'remaining_slices' => $rows,
        'allow' => ['remaining_slices' => true],
    ]);
    $blocked = $policy->evaluate($context, [
        'status' => 'claimable',
        'remaining_slices' => $rows,
        'allow' => ['remaining_slices' => false],
    ]);

    expect(collect($allowed['disclosures'])->firstWhere('key', 'remaining_slices')['value'])
        ->toBe($rows)
        ->and(collect($blocked['redactions'])->pluck('key')->all())
        ->toContain('remaining_slices');
});

it('allows admin operational disclosure and policy trace', function () {
    $policy = app(DefaultXRayDisclosurePolicy::class);
    $context = new XRayContextData(
        code: 'ABC123',
        actor: new XRayActorData(audience: 'admin'),
    );

    $decision = $policy->evaluate($context, [
        'status' => 'claimable',
        'amount' => '1000.00',
        'redirect_url' => 'https://example.com/redirect',
    ]);

    expect(collect($decision['disclosures'])->pluck('key')->all())->toContain('amount', 'redirect_url')
        ->and($decision['policy_trace'])->not->toBeEmpty();
});

it('passes through only safe presentation copy', function (): void {
    $policy = app(DefaultXRayDisclosurePolicy::class);
    $context = new XRayContextData(
        code: 'INVITE',
        actor: new XRayActorData(audience: 'guest'),
    );

    $decision = $policy->evaluate($context, [
        'status' => 'claimable',
        'presentation' => [
            'title' => 'Accept Invitation',
            'primary_action_label' => 'Accept Invitation',
            'subtitle' => 'Set up your workspace access.',
            'intent' => 'commissioning_invitation',
            'source' => 'flow_default',
            'issuer_id' => 'hidden',
            'provider_payload' => ['never' => 'public'],
        ],
    ]);

    expect($decision['presentation'])->toBe([
        'title' => 'Accept Invitation',
        'primary_action_label' => 'Accept Invitation',
        'subtitle' => 'Set up your workspace access.',
        'intent' => 'commissioning_invitation',
        'source' => 'flow_default',
    ]);
});
