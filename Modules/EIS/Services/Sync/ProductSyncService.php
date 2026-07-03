<?php

namespace Modules\EIS\Services\Sync;

use Illuminate\Support\Facades\Log;
use Modules\EIS\Services\Product\EisProductClient;
use Modules\EIS\Services\Product\ProductTransformer;
use Modules\EIS\Services\Product\ProductUpsertService;

class ProductSyncService
{
    public function __construct(
        protected EisProductClient $client,
        protected ProductTransformer $transformer,
        protected ProductUpsertService $upserter
    ) {}

    public function sync($settings, int $businessId)
    {
        $page = 1;

        while (true) {

            $response = $this->client->fetch($settings, $page);

            $items = $response['data'] ?? [];

            if (empty($items)) {
                break;
            }

            Log::info('SyncProductsJob: Fetched ' . count($items) . ' products for business_id: ' . $businessId . ' on page: ' . $page);

            foreach ($items as $item) {

                $data = $this->transformer->fromEis($item);

                $this->upserter->upsert(
                    $businessId,
                    $data,
                    $item['productCode'] ?? $item['sku'] ?? ''
                );
            }

            if (!($response['has_more'] ?? false)) {
                break;
            }

            $page++;
        }
    }
}