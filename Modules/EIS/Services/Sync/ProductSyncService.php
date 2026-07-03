<?php

namespace Modules\EIS\Services\Sync;

use AWS\CRT\Log;
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

            \Log::info("Fetched " . count($items) . " products from EIS for business ID: $businessId, page: $page");

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {

                $data = $this->transformer->fromEis($item);

                $this->upserter->upsert(
                    $businessId,
                    $data,
                    $item['id']
                );
            }

            if (!($response['has_more'] ?? false)) {
                break;
            }

            $page++;
        }
    }
}