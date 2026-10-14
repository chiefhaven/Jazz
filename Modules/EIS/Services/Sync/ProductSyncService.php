<?php

namespace Modules\EIS\Services\Sync;

use Illuminate\Support\Facades\Log;
use Modules\EIS\Services\Products\EisProductClient;
use Modules\EIS\Services\Products\OpeningStockInitializer;
use Modules\EIS\Services\Products\ProductTransformer;
use Modules\EIS\Services\Products\ProductUpsertService;

class ProductSyncService
{
    public function __construct(
        protected EisProductClient $client,
        protected ProductTransformer $transformer,
        protected ProductUpsertService $upserter
    ) {}

    public function sync($settings, int $businessId)
    {
        if (!is_object($settings) || $businessId <= 0
            || (!is_int($settings->business_id ?? null) && !is_string($settings->business_id ?? null))
            || (string) $settings->business_id !== (string) $businessId) {
            throw new \InvalidArgumentException('EIS product settings must belong to the import business.');
        }
        // Snapshot the same settings passed to every request; never query an arbitrary local site.
        $settings = clone $settings;
        $sentSite = EisProductClient::requestSiteId($settings);
        $requestSite = OpeningStockInitializer::siteId(['siteId' => $sentSite]);
        if ($requestSite === null || $requestSite !== (string) $sentSite) {
            throw new \InvalidArgumentException('EIS product request requires an exact valid site.');
        }
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
                    ?? null;

                if (!$eisId) {
                    Log::warning('Missing EIS product identifier', $item);
                    continue;
                }

                try {
                    $data = $this->transformer->fromEis($item, $eisId, $requestSite);

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