<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class EisConfiguration
 *
 * @property int $id
 * @property int $business_id
 * @property int|null $global_version
 * @property int|null $terminal_version
 * @property int|null $taxpayer_version
 * @property string|null $tpin
 * @property bool $is_vat_registered
 * @property string|null $tax_office_code
 * @property array|null $raw_response
 * @property Carbon|null $last_synced_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EisConfiguration extends Model
{
    protected $table = 'eis_configurations';

    protected $fillable = [
        'business_id',
        'global_version',
        'terminal_version',
        'taxpayer_version',
        'tpin',
        'is_vat_registered',
        'tax_office_code',
        'raw_response',
        'last_synced_at'
    ];

    protected $casts = [
        'business_id' => 'integer',
        'global_version' => 'integer',
        'terminal_version' => 'integer',
        'taxpayer_version' => 'integer',
        'is_vat_registered' => 'boolean',
        'raw_response' => 'array',
        'last_synced_at' => 'datetime'
    ];

    /**
     * Check if the configuration needs to be updated based on new version numbers.
     *
     * @param object $newConfig
     * @return bool
     */
    public function needsUpdate(object $newConfig): bool
    {
        return $this->global_version !== ($newConfig->globalConfiguration->versionNo ?? null) ||
               $this->terminal_version !== ($newConfig->terminalConfiguration->versionNo ?? null) ||
               $this->taxpayer_version !== ($newConfig->taxpayerConfiguration->versionNo ?? null);
    }

    /**
     * Scope a query to only include configurations that need syncing.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $hours
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNeedsSync($query, int $hours = 24)
    {
        return $query->where(function ($query) use ($hours) {
            $query->whereNull('last_synced_at')
                ->orWhere('last_synced_at', '<', now()->subHours($hours));
        });
    }

    /**
     * Get the tax rates from the raw response.
     *
     * @return array|null
     */
    public function getTaxRatesAttribute(): ?array
    {
        if (empty($this->raw_response)) {
            return null;
        }

        return $this->raw_response['data']['globalConfiguration']['taxrates'] ?? null;
    }

    /**
     * Get the terminal configuration from the raw response.
     *
     * @return array|null
     */
    public function getTerminalConfigurationAttribute(): ?array
    {
        if (empty($this->raw_response)) {
            return null;
        }

        return $this->raw_response['data']['terminalConfiguration'] ?? null;
    }

    /**
     * Get the taxpayer configuration from the raw response.
     *
     * @return array|null
     */
    public function getTaxpayerConfigurationAttribute(): ?array
    {
        if (empty($this->raw_response)) {
            return null;
        }

        return $this->raw_response['data']['taxpayerConfiguration'] ?? null;
    }
}