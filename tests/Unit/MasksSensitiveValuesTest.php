<?php

use LBHurtado\XRay\Support\MasksSensitiveValues;

it('masks common sensitive values', function () {
    $helper = new class
    {
        use MasksSensitiveValues;
    };

    expect($helper->maskMobile('639171234567'))->toBe('********4567')
        ->and($helper->maskEmail('juan@example.com'))->toBe('j***@example.com')
        ->and($helper->maskAccountNumber('1234567890'))->toBe('******7890')
        ->and($helper->fingerprint('PUBLIC'))->toStartWith('sha256:');
});
