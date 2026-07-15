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

            Log::info('EIS product page fetched', [
                'page' => $page,
                'count' => $response
            ]);

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {

                $eisId = $item['productCode']
                    ?? $item['id']
                    ?? $item['sku']
                    ?? null;

                if (!$eisId) {
                    Log::warning('Missing EIS product identifier', $item);
                    continue;
                }

                try {
                    $data = $this->transformer->fromEis($item);

                    $this->upserter->upsert(
                        $businessId,
                        $data,
                        $eisId
                    );

                } catch (\Throwable $e) {
                    Log::error('EIS product sync failed', [
                        'error' => $e->getMessage(),
                        'item' => $item,
                    ]);
                }
            }

            if (!($response['has_more'] ?? false)) {
                break;
            }

            $page++;
        }
    }
}