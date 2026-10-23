<?php

namespace Modules\EIS\Listeners;

use App\Events\StockAdjustmentCreatedOrModified;
use Modules\EIS\Services\Stock\StockOutboundService;

class QueueStockAdjustmentToEis
{
    public function __construct(private StockOutboundService $service) {}

    public function handle(StockAdjustmentCreatedOrModified $event): void
    {
        if ($event->action !== 'added' || !$event->eisAdjustmentReason) {
            return;
        }

        $this->service->enqueueLocalAdjustment(
            $event->stockAdjustment,
            $event->eisAdjustmentReason,
            $event->eisTerminalId,
            $event->eisSiteId
        );
    }
}
