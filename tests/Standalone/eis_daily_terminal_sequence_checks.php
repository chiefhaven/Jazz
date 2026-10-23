<?php

// Uses only the isolated SQLite location/terminal fixture; no application or HTTP.
require __DIR__.'\eis_location_terminal_checks.php';

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();
try {
    DB::table('eis_sales')->delete();
    Carbon::setTestNow(Carbon::parse('2026-10-23 23:59:59'));
    $firstDayNumbers = [];
    foreach ([1 => 1, 2 => 2] as $terminalId => $position) {
        foreach ([1 => 'B', 2 => 'C', 3 => 'D'] as $sequence => $encodedCount) {
            $number = $generator->generateInvoiceNumber(1, $generator->getDailyCount(1, $terminalId) + 1, $position, 100, $terminalId);
            $parts = explode('-', $number);
            locationCheck($parts[3] === $encodedCount, 'Each terminal independently issues B/C/D on the first day');
            locationCheck($generator->parseInvoiceNumber($number)['count'] === $sequence,
                'Base64 suffix decodes to terminal daily sequence '.$sequence);
            DB::table('eis_sales')->insert([
                'business_id' => 1, 'transaction_id' => 100 + $terminalId * 10 + $sequence,
                'location_id' => $terminalId, 'eis_setting_id' => $terminalId,
                'eis_invoice_number' => $number, 'fiscal_invoice_number' => 'CONFIRMED-RECEIPT',
                'status' => $sequence === 1 ? 'submitted' : ($sequence === 2 ? 'failed' : 'pending'),
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $firstDayNumbers[$terminalId] = $number;
        }
    }
    Carbon::setTestNow(Carbon::parse('2026-10-24 00:00:00'));
    foreach ([1 => 1, 2 => 2] as $terminalId => $position) {
        locationCheck($generator->getDailyCount(1, $terminalId) === 0, 'Every terminal count resets at midnight');
        $number = $generator->generateInvoiceNumber(1, $generator->getDailyCount(1, $terminalId) + 1, $position, 100, $terminalId);
        $parts = explode('-', $number);
        $previous = explode('-', $firstDayNumbers[$terminalId]);
        locationCheck($parts[3] === 'B' && $parts[0] === $previous[0] && $parts[1] === $previous[1]
            && $parts[2] === $generator->base10ToBase64($generator->toJulianDate(now())),
            'Next-day first invoice starts at B while taxpayer/terminal and Julian-date format are preserved');
        DB::table('eis_sales')->insert([
            'business_id' => 1, 'transaction_id' => 200 + $terminalId,
            'location_id' => $terminalId, 'eis_setting_id' => $terminalId,
            'eis_invoice_number' => $number, 'status' => 'pending',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        locationCheck($generator->getDailyCount(1, $terminalId) === 1, 'Next-day counter excludes all previous-day allocations');
    }
    $before = DB::table('eis_sales')->where('transaction_id', 112)->value('eis_invoice_number');
    DB::table('eis_sales')->where('transaction_id', 112)->update(['status' => 'submitted', 'updated_at' => now()]);
    locationCheck($generator->getDailyCount(1, 1) === 1
        && DB::table('eis_sales')->where('transaction_id', 112)->value('eis_invoice_number') === $before,
        'Yesterday retry keeps its invoice and does not consume today sequence');
} finally {
    Carbon::setTestNow();
    DB::rollBack();
}
echo "PASS: {$checks} cumulative daily terminal sequence checks (SQLite, no network).\n";
