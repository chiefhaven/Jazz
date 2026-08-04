<?php

namespace Modules\EIS\Models;

use Illuminate\Database\Eloquent\Model;

class EisSale extends Model
{
    protected $table = 'eis_sales';

    protected $fillable = [
        'business_id',
        'transaction_id',
        'invoice_number',
        'fiscal_invoice_number',
        'receipt_number',
        'receipt_signature',
        'qr_code',
        'status',
        'request_payload',
        'response_payload',
        'error_message',
        'submitted_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class);
    }

    /**
     * Check if offline sale limits are within allowed thresholds
     * 
     * @param int $business_id
     * @param float $sale_amount
     * @return array Returns ['success' => bool, 'message' => string, 'code' => string|null, 'details' => array|null]
     */
    public static function checkOfflineLimits($business_id, $sale_amount)
    {
        try {
            // Get business settings with terminal configuration
            $eisSetting = EisSetting::where('business_id', $business_id)
                ->with('terminalConfiguration')
                ->first();
                
            if (!$eisSetting) {
                \Log::warning('Business settings not found for offline limit check', [
                    'business_id' => $business_id,
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Business settings not found',
                    'code' => 'BUSINESS_NOT_FOUND',
                    'details' => null
                ];
            }
            
            // Check if terminal configuration exists
            $terminalConfig = $eisSetting->terminalConfiguration;
            if (!$terminalConfig) {
                \Log::warning('Terminal configuration not found for offline limit check', [
                    'business_id' => $business_id,
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Terminal configuration not found',
                    'code' => 'TERMINAL_CONFIG_NOT_FOUND',
                    'details' => null
                ];
            }
            
            // Get offline limits from terminal configuration
            $offlineLimit = $terminalConfig->max_cummulative_amount ?? 0;
            $maxTransactionAge = $terminalConfig->max_transaction_age ?? 0;
            
            // If no limits set, allow sale
            if ($offlineLimit == 0 && $maxTransactionAge == 0) {
                return [
                    'success' => true,
                    'message' => 'No limits configured',
                    'code' => 'NO_LIMITS',
                    'details' => [
                        'limit_type' => 'unlimited',
                        'sale_amount' => $sale_amount
                    ]
                ];
            }
            
            // Get total unsubmitted sales for this business
            $totalUnsubmitted = self::where('eis_sales.business_id', $business_id)
            ->where('eis_sales.status', '!=', 'submitted')
            ->join('transactions', 'eis_sales.transaction_id', '=', 'transactions.id')
            ->sum('transactions.final_total');
            
            // Get the oldest unsubmitted sale
            $oldestUnsubmittedSale = self::where('eis_sales.business_id', $business_id)
                ->where('eis_sales.status', '!=', 'submitted')
                ->orderBy('eis_sales.created_at', 'asc')
                ->first();
            
            // Calculate age difference in hours
            $ageDifferenceInHours = $oldestUnsubmittedSale 
                ? now()->diffInHours($oldestUnsubmittedSale->created_at) 
                : 0;
            
            // Check age limit (max_transaction_age)
            if ($maxTransactionAge > 0 && $ageDifferenceInHours > $maxTransactionAge) {
                \Log::warning('Transaction age limit exceeded', [
                    'business_id' => $business_id,
                    'max_age_limit' => $maxTransactionAge,
                    'age_difference' => $ageDifferenceInHours,
                    'oldest_sale_id' => $oldestUnsubmittedSale->id ?? null,
                    'oldest_sale_date' => $oldestUnsubmittedSale->created_at ?? null
                ]);
                
                return [
                    'success' => false,
                    'message' => "Unsubmitted sale exceeds maximum transaction age of {$maxTransactionAge} hours. Please submit pending sales.",
                    'code' => 'TRANSACTION_AGE_EXCEEDED',
                    'details' => [
                        'max_age_limit' => $maxTransactionAge,
                        'age_difference' => $ageDifferenceInHours,
                        'oldest_sale_date' => $oldestUnsubmittedSale->created_at ?? null,
                        'total_unsubmitted' => $totalUnsubmitted,
                        'limit_type' => 'age_limit'
                    ]
                ];
            }
            
            // Check cumulative amount limit (max_cummulative_amount)
            if ($offlineLimit > 0) {
                $totalAfterSale = $totalUnsubmitted + $sale_amount;
                
                if ($totalAfterSale > $offlineLimit) {
                    $remaining = $offlineLimit - $totalUnsubmitted;
                    
                    \Log::warning('Cumulative amount limit exceeded', [
                        'business_id' => $business_id,
                        'cumulative_limit' => $offlineLimit,
                        'current_total' => $totalUnsubmitted,
                        'sale_amount' => $sale_amount,
                        'total_after_sale' => $totalAfterSale,
                        'remaining' => $remaining
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => "Cumulative offline amount limit exceeded. Remaining: " . number_format($remaining, 2),
                        'code' => 'CUMULATIVE_LIMIT_EXCEEDED',
                        'details' => [
                            'cumulative_limit' => $offlineLimit,
                            'current_total' => $totalUnsubmitted,
                            'sale_amount' => $sale_amount,
                            'total_after_sale' => $totalAfterSale,
                            'remaining' => $remaining,
                            'limit_type' => 'cumulative_amount'
                        ]
                    ];
                }
            }
            
            // All checks passed
            return [
                'success' => true,
                'message' => 'Offline limits check passed',
                'code' => 'LIMITS_PASSED',
                'details' => [
                    'cumulative_limit' => $offlineLimit,
                    'current_total' => $totalUnsubmitted,
                    'sale_amount' => $sale_amount,
                    'total_after_sale' => $totalUnsubmitted + $sale_amount,
                    'remaining' => $offlineLimit - ($totalUnsubmitted + $sale_amount),
                    'max_age_limit' => $maxTransactionAge,
                    'age_difference' => $ageDifferenceInHours,
                    'oldest_sale_date' => $oldestUnsubmittedSale->created_at ?? null,
                    'is_within_limits' => true
                ]
            ];
            
        } catch (\Exception $e) {
            // Log error but allow sale to proceed (fail-open strategy)
            \Log::error('Error checking offline limits - allowing sale', [
                'business_id' => $business_id,
                'sale_amount' => $sale_amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return success to not block sales if there's an error checking limits
            return [
                'success' => true,
                'message' => 'Error checking limits, allowing sale to proceed',
                'code' => 'CHECK_ERROR',
                'details' => [
                    'error' => $e->getMessage(),
                    'fallback' => 'fail-open'
                ]
            ];
        }
    }
}
