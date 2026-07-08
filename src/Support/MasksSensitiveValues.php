<?php

namespace LBHurtado\XRay\Support;

trait MasksSensitiveValues
{
    public function maskMobile(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (strlen($digits) <= 4) {
            return str_repeat('*', strlen($digits));
        }

        return str_repeat('*', max(strlen($digits) - 4, 0)).substr($digits, -4);
    }

    public function maskEmail(?string $value): ?string
    {
        if (! $value || ! str_contains($value, '@')) {
            return $value ? '***' : null;
        }

        [$name, $domain] = explode('@', $value, 2);

        return substr($name, 0, 1).'***@'.$domain;
    }

    public function maskAccountNumber(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return str_repeat('*', max(strlen($value) - 4, 0)).substr($value, -4);
    }

    public function fingerprint(?string $value): ?string
    {
        return $value ? 'sha256:'.hash('sha256', $value) : null;
    }
}
