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
