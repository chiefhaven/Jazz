<?php

namespace Modules\EIS\Services\Sync;

use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisProductMap;
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
        $seenProductIds = [];
        $syncIncomplete = false;

        while (true) {
            $response = $this->client->fetch($settings, $page);

            if (!is_array($response) || !array_key_exists('data', $response) || !is_array($response['data'])) {
                throw new \UnexpectedValueException('EIS product response has an invalid data collection.');
            }

            $items = $response['data'];

            Log::info('EIS product page fetched', [
                'page' => $page,
                'count' => count($items),
            ]);

            if (empty($items)) {
                if ($response['has_more'] ?? false) {
                    $syncIncomplete = true;
                    Log::warning('EIS product page is empty but more pages were reported', [
                        'business_id' => $businessId,
                        'page' => $page,
                    ]);
                }
                break;
            }

            foreach ($items as $item) {
                if (!is_array($item)) {
                    $syncIncomplete = true;
                    Log::warning('Invalid EIS product item', [
                        'business_id' => $businessId,
                        'page' => $page,
                    ]);
                    continue;
                }

                $eisId = $item['productCode']
                    ?? $item['id']
                    ?? null;

                if (!$eisId) {
                    $syncIncomplete = true;
                    Log::warning('Missing EIS product identifier', $item);
                    continue;
                }

                $eisId = (string) $eisId;
                try {
                    $data = $this->transformer->fromEis($item, $eisId, $requestSite);

                    $this->upserter->upsert(
                        $businessId,
                        $data,
                        $eisId
                    );

                    $seenProductIds[$eisId] = true;
                } catch (\Throwable $e) {
                    $syncIncomplete = true;
                    Log::error('EIS product sync failed', [
                        'business_id' => $businessId,
                        'eis_product_id' => $eisId,
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

        if ($syncIncomplete || empty($seenProductIds)) {
            Log::warning('Skipping stale EIS product cleanup because the import was incomplete or empty', [
                'business_id' => $businessId,
                'processed_count' => count($seenProductIds),
                'incomplete' => $syncIncomplete,
            ]);

            return;
        }

        $this->softDeleteMissingProducts($businessId, array_keys($seenProductIds));
    }

    private function softDeleteMissingProducts(int $businessId, array $seenProductIds): void
    {
        $staleMaps = EisProductMap::query()
            ->where('business_id', $businessId)
            ->whereNotIn('eis_product_id', $seenProductIds)
            ->get(['id', 'product_id']);

        if ($staleMaps->isEmpty()) {
            return;
        }

        $deletedProductCount = DB::transaction(function () use ($businessId, $staleMaps): int {
            $deletedProductCount = Product::query()
                ->where('business_id', $businessId)
                ->whereIn('id', $staleMaps->pluck('product_id'))
                ->delete();

            EisProductMap::query()
                ->whereIn('id', $staleMaps->pluck('id'))
                ->delete();

            return $deletedProductCount;
        });

        Log::info('Soft-deleted EIS products missing from the latest feed', [
            'business_id' => $businessId,
            'product_count' => $deletedProductCount,
        ]);
    }
}