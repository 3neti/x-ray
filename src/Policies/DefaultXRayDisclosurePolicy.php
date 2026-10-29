<?php

namespace LBHurtado\XRay\Policies;

use Illuminate\Support\Arr;
use LBHurtado\XRay\Contracts\XRayDisclosurePolicyContract;
use LBHurtado\XRay\Data\XRayContextData;
use LBHurtado\XRay\Support\MasksSensitiveValues;

class DefaultXRayDisclosurePolicy implements XRayDisclosurePolicyContract
{
    use MasksSensitiveValues;

    public function evaluate(XRayContextData $context, array $projection): array
    {
        $audience = $context->actor->audience ?: config('x-ray.disclosure.default_audience', 'guest');
        $rules = config("x-ray.disclosure.$audience", config('x-ray.disclosure.guest', []));

        $status = Arr::get($projection, 'status', Arr::get($projection, 'voucher.status', 'unknown'));
        $visible = (bool) Arr::get($rules, 'show_exists', true) && $status !== 'not_found';

        $disclosures = [];
        $requirements = [];
        $redactions = [];
        $warnings = [];
        $policyTrace = [];

        $this->maybeDisclose('status', $status, 'Status', Arr::get($rules, 'show_status'), $disclosures, $redactions, $audience, $policyTrace);
        $this->maybeDisclose('amount', Arr::get($projection, 'amount'), 'Amount', Arr::get($rules, 'show_amount'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('value_use', Arr::get($projection, 'value_use'), 'Value use', Arr::get($rules, 'show_amount'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('issuer', Arr::get($projection, 'issuer'), 'Issuer', Arr::get($rules, 'show_issuer'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('funding_source', Arr::get($projection, 'funding_source'), 'Money source', Arr::get($rules, 'show_funding_source'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('provider', Arr::get($projection, 'provider'), 'Payout provider', Arr::get($rules, 'show_provider'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('issued_at', Arr::get($projection, 'issued_at'), 'Issued', Arr::get($rules, 'show_issued_at'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('expires_at', Arr::get($projection, 'expires_at'), 'Expires', Arr::get($rules, 'show_expires_at'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('reference', Arr::get($projection, 'reference'), 'Pay Code', Arr::get($rules, 'show_reference'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('service_info', Arr::get($projection, 'service_info'), 'Service information', Arr::get($rules, 'show_service_info'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('remaining_slices', Arr::get($projection, 'remaining_slices'), 'Remaining Slices', Arr::get($rules, 'show_remaining_slices'), $disclosures, $redactions, $audience, $policyTrace, $projection);
        $this->maybeDisclose('redirect_url', Arr::get($projection, 'redirect_url'), 'Redirect URL', Arr::get($rules, 'show_redirect_url'), $disclosures, $redactions, $audience, $policyTrace, $projection);

        if (Arr::get($rules, 'show_requirements', true)) {
            $requirements = array_values(Arr::get($projection, 'requirements', []));
            $policyTrace[] = 'requirements:visible';
        }

        $stages = [];
        $stageRule = Arr::get($rules, 'show_rider_preclaim', false);
        if ($this->allows($stageRule, $projection, 'rider_preclaim')) {
            $stages = array_values(Arr::get($projection, 'stages', Arr::get($projection, 'rider.preClaim.stages', [])));
            $policyTrace[] = 'rider_preclaim:visible';
        } else {
            $redactions[] = ['key' => 'rider_preclaim', 'reason' => 'policy', 'audience' => $audience];
            $policyTrace[] = 'rider_preclaim:redacted';
        }

        $nextActions = array_values(Arr::get($projection, 'next_actions', []));
        $presentation = $this->safePresentation(
            Arr::get($projection, 'presentation', []),
        );

        if (! Arr::get($rules, 'show_policy_trace', false)) {
            $policyTrace = [];
        }

        return [
            'visible' => $visible,
            'disclosures' => $disclosures,
            'requirements' => $requirements,
            'redactions' => $redactions,
            'stages' => $stages,
            'next_actions' => $nextActions,
            'presentation' => $presentation,
            'warnings' => $warnings,
            'policy_trace' => $policyTrace,
        ];
    }

    protected function maybeDisclose(
        string $key,
        mixed $value,
        string $label,
        mixed $rule,
        array &$disclosures,
        array &$redactions,
        string $audience,
        array &$policyTrace,
        array $projection = [],
    ): void {
        if ($value !== null && $value !== '' && $value !== [] && $this->allows($rule, $projection, $key)) {
            $disclosures[] = [
                'key' => $key,
                'label' => $label,
                'value' => $this->safeValue($key, $value),
                'visibility' => 'visible',
            ];
            $policyTrace[] = "$key:visible";

            return;
        }

        $redactions[] = ['key' => $key, 'reason' => 'policy', 'audience' => $audience];
        $policyTrace[] = "$key:redacted";
    }

    protected function allows(mixed $rule, array $projection, string $key): bool
    {
        if ($rule === true) {
            return true;
        }

        if ($rule === 'if_allowed_by_voucher') {
            return (bool) Arr::get($projection, "allow.$key", false);
        }

        return false;
    }

    protected function safeValue(string $key, mixed $value): mixed
    {
        return match ($key) {
            'mobile' => $this->maskMobile((string) $value),
            'email' => $this->maskEmail((string) $value),
            'account_number' => $this->maskAccountNumber((string) $value),
            'service_info' => $this->safeServiceInfo($value),
            'value_use' => $this->safeValueUse($value),
            default => $value,
        };
    }

    protected function safeServiceInfo(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $fields = collect($value)
            ->only(['host_name', 'host_version', 'x_change_version', 'form_flow_version', 'x_ray_version', 'publisher'])
            ->filter(static fn (mixed $item): bool => is_string($item) && trim($item) !== '')
            ->map(static fn (string $item): string => mb_substr(trim($item), 0, 120))
            ->all();
        foreach (['authors', 'contributors'] as $key) {
            if (! is_array($value[$key] ?? null)) {
                continue;
            }

            $fields[$key] = array_values(array_filter(array_map(
                static fn (mixed $item): ?string => is_string($item) && trim($item) !== ''
                    ? mb_substr(trim($item), 0, 120)
                    : null,
                array_slice($value[$key], 0, 10),
            )));
        }

        return $fields;
    }

    protected function safeValueUse(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $mode = $value['mode'] ?? null;
        $basis = $value['amount_basis'] ?? null;
        if (! in_array($mode, ['whole', 'flexible', 'equal', 'scheduled', 'stored_value'], true)
            || ! in_array($basis, ['whole', 'remaining', 'available_now', 'next_available', 'starting', 'unavailable'], true)) {
            return [];
        }

        $safe = [
            'schema' => 'x-change.claim-value-use.v1',
            'mode' => $mode,
            'amount_basis' => $basis,
            'currency' => 'PHP',
        ];
        foreach (['amount_minor', 'minimum_minor', 'claims_remaining', 'portion_number', 'portion_count'] as $key) {
            $number = $value[$key] ?? null;
            if (is_int($number) && $number >= 0) {
                $safe[$key] = $number;
            }
        }

        $availableAt = $value['available_at'] ?? null;
        if (is_string($availableAt) && strlen($availableAt) <= 40 && strtotime($availableAt) !== false) {
            $safe['available_at'] = $availableAt;
        }

        $terms = $value['collection_terms'] ?? null;
        if (is_array($terms) && $mode === 'stored_value') {
            $safe['collection_terms'] = ['amount' => 'collector_sets_amount'];
            if (is_int($terms['otp_above_minor'] ?? null) && $terms['otp_above_minor'] > 0) {
                $safe['collection_terms']['otp_above_minor'] = $terms['otp_above_minor'];
            }
        }

        return $safe;
    }

    protected function safePresentation(mixed $presentation): array
    {
        if (! is_array($presentation)) {
            return [];
        }

        return collect($presentation)
            ->only(['title', 'primary_action_label', 'subtitle', 'intent', 'source'])
            ->filter(static fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(static fn (string $value): string => trim($value))
            ->all();
    }
}
