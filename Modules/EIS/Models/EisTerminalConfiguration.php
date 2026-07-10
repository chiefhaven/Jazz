<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TerminalConfiguration extends Model
{
    protected $table = 'terminal_configurations';

    protected $fillable = [
        'configuration_id',
        'version',
        'terminal_label',
        'is_active',
        'email_address',
        'phone_number',
        'trading_name',
        'address_lines',
        'raw_data',
        'last_synced_at'
    ];

    protected $casts = [
        'version' => 'integer',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime'
    ];

    /**
     * Get the configuration that owns the terminal.
     */
    public function configuration(): BelongsTo
    {
        return $this->belongsTo(EisConfiguration::class, 'configuration_id');
    }

    /**
     * Get the terminal site.
     */
    public function terminalSite(): HasOne
    {
        return $this->hasOne(TerminalSite::class, 'terminal_configuration_id');
    }

    /**
     * Get the offline limit.
     */
    public function offlineLimit(): HasOne
    {
        return $this->hasOne(OfflineLimit::class, 'terminal_configuration_id');
    }

    /**
     * Get address lines as array.
     */
    public function getAddressLinesAttribute($value): array
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Get full address as string.
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', $this->address_lines);
    }

    /**
     * Check if terminal has site.
     */
    public function hasSite(): bool
    {
        return $this->terminalSite !== null;
    }

    /**
     * Check if terminal has offline limit.
     */
    public function hasOfflineLimit(): bool
    {
        return $this->offlineLimit !== null;
    }

    /**
     * Scope for active terminals.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive terminals.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for terminals by trading name.
     */
    public function scopeByTradingName($query, string $name)
    {
        return $query->where('trading_name', 'like', "%{$name}%");
    }

    /**
     * Scope for terminals by email.
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email_address', $email);
    }

    /**
     * Scope for terminals synced recently.
     */
    public function scopeSyncedRecently($query, int $hours = 24)
    {
        return $query->where('last_synced_at', '>=', now()->subHours($hours));
    }

    /**
     * Get terminal label or trading name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->terminal_label ?? $this->trading_name ?? 'Terminal';
    }

    /**
     * Get terminal status as string.
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get terminal status with badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active 
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-danger">Inactive</span>';
    }

    /**
     * Check if terminal has valid email.
     */
    public function hasValidEmail(): bool
    {
        return !empty($this->email_address) && filter_var($this->email_address, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if terminal has valid phone number.
     */
    public function hasValidPhone(): bool
    {
        return !empty($this->phone_number) && preg_match('/^[0-9+\-\s()]{10,15}$/', $this->phone_number);
    }

    /**
     * Get terminal summary.
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'configuration_id' => $this->configuration_id,
            'display_name' => $this->display_name,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'trading_name' => $this->trading_name,
            'email_address' => $this->email_address,
            'phone_number' => $this->phone_number,
            'address' => $this->full_address,
            'version' => $this->version,
            'has_site' => $this->hasSite(),
            'has_offline_limit' => $this->hasOfflineLimit(),
            'last_synced_at' => $this->last_synced_at,
            'site' => $this->terminalSite ? $this->terminalSite->toArray() : null,
            'offline_limit' => $this->offlineLimit ? $this->offlineLimit->toArray() : null
        ];
    }
}