<?php

namespace App\Services;

use App\Models\Reseller;
use App\Models\ReferralCode;
use Illuminate\Support\Str;

class PromoCodeService
{
    public function generateCode(Reseller $reseller, ?string $label = null): ReferralCode
    {
        do {
            // Take up to 4 alpha chars from name
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $reseller->name), 0, 4));
            if (strlen($prefix) < 3) {
                $prefix = strtoupper(Str::random(4));
            }
            $suffix = strtoupper(Str::random(4));
            $code = "{$prefix}-{$suffix}";
        } while (ReferralCode::where('code', $code)->exists());

        return ReferralCode::create([
            'reseller_id' => $reseller->id,
            'code'        => $code,
            'label'       => $label ?? 'Default Auto-generated',
            'status'      => 'active',
        ]);
    }

    public function generateResellerCode(): string
    {
        do {
            $code = 'RES-' . str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Reseller::where('reseller_code', $code)->exists());

        return $code;
    }
}
