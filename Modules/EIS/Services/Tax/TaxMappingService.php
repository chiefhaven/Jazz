<?php

namespace Modules\EIS\Services\Tax;

use Modules\EIS\Models\EisTaxMapping;

class TaxMappingService
{
    public function resolve(int $businessId, ?int $taxId): string
    {
        if (!$taxId) {
            return 'EXEMPT';
        }

        $map = EisTaxMapping::where('business_id', $businessId)
            ->where('tax_id', $taxId)
            ->first();

        return $map?->eis_tax_rate_id ?? 'EXEMPT';
    }
}