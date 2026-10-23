# EIS Terminal Activation and Configuration Fixes

## Settings → MRA EIS terminal management

### Environment and registered POS product

```dotenv
EIS_ENV=development
EIS_BASE_URL=<EIS API base URL for the selected environment>
EIS_PRODUCT_ID=null
EIS_PRODUCT_VERSION=1.0.0
```

Use `EIS_ENV=production` and the MRA-registered `EIS_PRODUCT_ID` for production. Missing product IDs block production activation before HTTP; browser input cannot override the configured ID/version. In development, a null product ID uses the application name so the required `environment.pos.productID` remains nonempty. `dev` and the legacy `sandbox` mode are development aliases; other modes are rejected. Mode does not rewrite `EIS_BASE_URL`: configure the matching endpoint explicitly.

The [EIS Swagger schema](https://dev-eis-api.mra.mw/swagger/index.html) requires nonempty `productID` and `productVersion` in the POS environment. Clear/rebuild Laravel's configuration cache and restart long-running queue workers after changing these environment values.

Activation normalizes operating-system names before contacting EIS. Browser platform aliases such as `Win32`/`MacIntel` are not sent as OS names; the settings form lets the server identify the OS from the request user agent. Unrecognized operating systems are rejected locally with an actionable error.

The settings tab lists terminals at the signed-in user's permitted locations, including restricted administrators. Multiple terminals may belong to a location; **Set default** designates the location's terminal for sales. Terminal IDs below are local `eis_settings.id` values, not EIS device IDs or configuration IDs.

All management routes require a session-authenticated account with `business_settings.access`; POST requests require the normal web CSRF token. Business identity comes from the account, never from a client-supplied business ID. Locations and terminals must belong to that business and be permitted for that user (`all` is honored).

| Method | Path | Route name | Input |
| --- | --- | --- | --- |
| GET | `/eis/terminals` | `eis.terminals.index` | None |
| POST | `/eis/terminals/activate` | `eis.terminals.activate` | Required `terminal_activation_code`; optional `environment` object. Location comes from EIS. |
| POST | `/eis/terminals/{terminalId}/latest-config` | `eis.terminals.latest-config` | None |
| POST | `/eis/terminals/{terminalId}/deactivate` | `eis.terminals.deactivate` | Optional `reason` |
| POST | `/eis/terminals/{terminalId}/renew-tokens` | `eis.terminals.renew-tokens` | None; uses this terminal's stored JWT |
| POST | `/eis/terminals/{terminalId}/default` | `eis.terminals.default` | None; updates this terminal's own location |

Listing returns `{success: true, data: {locations: [...], terminals: [...]}}`. Terminal entries contain `id`, `location_id`, `location_name`, `terminal_label`, `device_id`, `is_active`, and `is_default`. Location entries contain `id`, `name`, and `default_eis_setting_id`. Unassigned legacy terminals are excluded until reconciled. Management responses never include JWTs or secret keys.

Mutations return `{success, message, msg}`; service failures use HTTP 422. Normal authentication, authorization, validation, and missing-record responses apply. Status is active only when both the local setting's `status` and terminal configuration's `is_active` are true. Default designation requires both flags to be active; inactive terminals cannot be selected as a new default. The old singleton `eis.web.terminal.*` web routes were replaced; API routes and their authentication remain separate and unchanged.

Isolated regression checks (SQLite memory database; no application boot or live EIS calls):

```powershell
php -d error_reporting=8191 tests\Standalone\eis_terminal_management_checks.php
```

## Location-owned terminal schema and rollout

The current design supersedes the historical singleton behavior described below:

- A business owns many locations. A location owns many terminals. Activation uses EIS `terminalSite.siteId` and `siteName` to match an existing location in that business, or creates one with default invoice/layout/payment settings and a location permission. A manually selected location is not required.
- Reactivating a terminal assigned to another site moves that same terminal and configuration to the API-assigned location. Cross-business terminal theft is rejected. The old location's default is cleared if it pointed to the moved terminal; choose a default for the destination explicitly.
- Historical sales, retry records, and stock are not moved or rewritten. Retries for a terminal that has moved away from the sale's original site fail explicitly for reconciliation rather than submitting with another site's identity.
- `eis_settings.id` is the terminal identity. Settings store `location_id`; configurations, sales, and retry records store both `location_id` and `eis_setting_id`. A terminal has one configuration.
- `business_locations.default_eis_setting_id` explicitly chooses the terminal for new sales. Activation does not silently replace a different terminal or switch the default. Missing/inactive defaults block submission; choose **Set default** after activation.
- Existing fiscal sales remain pinned to their recorded terminal, even after the location default changes. Unreconciled historical sales fail explicitly rather than being reassigned.
- Local deactivation disables both the terminal and its settings. Configuration refreshes cannot silently re-enable it; reactivation or an explicit toggle is required.
- In `EIS_ENV=development` (aliases `dev` and `sandbox`), a configured, locally enabled terminal remains operational even when EIS reports `isActiveTerminal=false`. The remote value is preserved for diagnostics. Terminal lists, default selection, status, and sale submission use the same policy. Production still requires the remote active flag; local deactivation and missing configuration block use in every environment.
- `eis_sales.eis_invoice_number` retains the generated invoice identity even when the confirmed receipt overwrites `fiscal_invoice_number`. Only the last invoice component changes: today's allocated invoice count for the selected terminal plus one. Pending and failed allocations count too; retries reuse the number.
- Terminal allocation and sale submission use atomic cache locks. Use a shared lock-capable cache across all web/queue servers (file cache is suitable only when processes share the same cache filesystem). Do not use the array/null cache driver in production.
- Product identities/catalog mappings remain business-wide; opening-stock initialization and stock quantities are location-specific. All users, including administrators, see quantities only at permitted tenant locations. A product pull must not delete another location's stock or shared catalog.
- New locations reuse default invoice schemes/layouts and payment settings. Configuration reconciliation updates the API-assigned location and preserves its existing invoice/address choices. Duplicate locations with the same site ID are rejected for reconciliation. Activation requires access to both the terminal's previous location and the API-assigned location; creating a new API site requires `access_all_locations`.
- Bulk submission from the sales screen is restricted to permitted locations. Each job resolves its own sale's location and terminal; no arbitrary business-wide token is used. The global badge shows **Multiple terminals** instead of claiming one selected terminal represents all locations.

### Desktop Connector tills

Named tills are separate from EIS terminals and cash-register sessions. `connector_tills` links a business/location/name to an EIS setting. An administrator with `business_settings.access` can create or update a named till from MbiraPOS Connector settings, then select it for that desktop installation (encrypted for the current Windows user, retained across cashier sign-ins). Updating an existing name at the same location changes that shared till's terminal, including for other devices selecting the same till.

- `GET /connector/api/tills` lists active tills and terminal IDs at the authenticated user's permitted locations; it never returns EIS credentials.
- `POST /connector/api/tills` accepts `name`, `location_id`, and `eis_setting_id`. Business identity comes from authentication. Settings permission, location permission and terminal ownership are checked.
- MbiraPOS sends `X-POS-Till: <id>` on operational Connector requests. The API validates tenant, active till, location access and sale/query location. An invalid/unpermitted till is rejected rather than silently replaced. Older clients without the header retain location-default routing.
- Retail creation/update, saved-bill checkout and restaurant creation/clearing persist `transactions.connector_till_id` within the sale/payment transaction. Split children retain till provenance. Workspace location is pinned to the saved till; changing the device assignment requires desktop admin settings.
- New fiscal submissions use the till's operational terminal. When that terminal is unavailable (including site reassignment), the same location's operational default is used and a notice is logged. If neither is usable, fiscal submission is blocked. Offline-limit checks use the same till selection.
- Once EIS sale/retry tracking exists, its recorded terminal takes precedence over till/default changes. An unavailable recorded terminal blocks retry instead of changing fiscal identity.

Deploy migration `Modules/Connector/Database/Migrations/2026_10_23_200000_create_connector_tills.php` before using desktop till settings. No existing locations, terminal defaults or historical sales are reassigned by this migration.

### Migration

`2026_10_23_211000_add_transaction_fiscal_receipt_projection.php` restores missing `transactions.is_fiscalized`, `fiscal_invoice_number`, and `receipt_number` columns without altering existing columns. Receipt identifiers are strings. Its rollback deliberately retains compatibility columns. A retry of an already-submitted sale repairs the local transaction receipt from tenant-scoped submitted EIS tracking without resubmitting to EIS. If the queue failed after acceptance, verify the saved response/tracking first; do not reset submitted tracking to pending. Apply this migration on each affected deployment/database before retrying the failed job.

`EIS_BASE_URL` in `.env`, accessed through Laravel's `config('eis.base_url')`, is the runtime endpoint source for EIS clients, including credential refresh and signed GET requests. The database column is retained for legacy schema compatibility, not as a per-terminal endpoint override. After changing `.env`, rebuild or clear Laravel's configuration cache as appropriate for the deployment.

Activation supports schemas with or without the legacy `eis_settings.base_url` column. It populates that field only when present (for older schemas where it is required); the endpoint is always taken from configuration.

The follow-up `2026_10_23_171800_restore_eis_settings_base_url.php` restores the original `eis_settings.base_url` column on installations where it is missing. Empty endpoints are populated from `eis.base_url`; existing terminal endpoints are preserved. Its rollback intentionally retains this original-schema column.

Back up the database and run the normal deployment migration process during a maintenance window:

```powershell
php artisan module:migrate EIS
```

The new migration is `2026_10_23_140000_scope_eis_to_location_terminals.php`. It adds ownership columns without deleting legacy records, backfills only exact site/terminal/invoice matches, and designates a default only where a location has exactly one mapped terminal. On MySQL it converts the affected business/location and fiscal ownership tables to InnoDB so activation and credential updates can roll back atomically. Engine conversion may rebuild/lock large tables; storage engines are not reverted on rollback.

Review `eis_migration_issues` after migration. It records the source table, source ID, business ID, and reason for every unresolved mapping. Reconcile those records against verified historical terminal/site identities; do not assign them to an arbitrary first location. Unmapped terminals remain excluded from management until reconciled. A rollback is rejected once a business has multiple mapped terminals.

No deployment migration or real EIS activation is performed by the standalone checks:

```powershell
php -d error_reporting=24575 tests\Standalone\eis_location_terminal_checks.php
php -d error_reporting=24575 tests\Standalone\eis_terminal_isolation_checks.php
php -d error_reporting=24575 tests\Standalone\eis_terminal_management_checks.php
php -d error_reporting=24575 tests\Standalone\eis_submission_retry_checks.php
php -d error_reporting=24575 tests\Standalone\eis_pos_validation_checks.php
```

Legacy API terminal/configuration/tax-rate actions accept `terminal_id` (local settings ID); token-driven actions validate token ownership. Omitted selection works only for an unambiguous single-terminal business and still enforces location permissions. Activation resolves the location from the API site rather than requiring `location_id`.

## Historical fixes (ownership behavior superseded above)
Fixed three critical issues in the Jazz POS EIS integration system related to terminal activation, configuration syncing, and settings display.

---

## Issues Fixed

### 1. Foreign Key Constraint Violations During Terminal Activation
**Issue**: Terminal activation failed with `SQLSTATE[23000]` foreign key constraint violation when attempting to insert business location.

**Root Cause**: 
- Missing `invoice_scheme_id` and `invoice_layout_id` in the INSERT statement
- Missing required fields: `state`, `city`, `zip_code` 
- Hardcoded `sale_invoice_scheme_id => 1` that didn't exist

**Solution** (Modules/EIS/Services/Configuration/ConfigurationSyncService.php):
- Auto-create default invoice scheme if missing (lines 1200-1210)
- Auto-create default invoice layout if missing (lines 1211-1220)
- Provide all required location fields with sensible defaults (lines 1226-1233)
- Result: All foreign key constraints now satisfied

### 2. Missing Branch Information in EIS Settings Display
**Issue**: EIS settings page showed `Branch: N/A` despite being requested to display business location name.

**Root Cause**: TerminalActivationController.details() endpoint wasn't returning business location information

**Solution** (Modules/EIS/Http/Controllers/API/TerminalActivationController.php):
- Added business location lookup in details() method (around line 740)
- Added `'branch' => $businessLocation?->name ?? null` to response
- Frontend now receives and displays the correct branch name

### 3. Get Latest Config Using Job Queue Instead of Direct API Call
**Issue**: `fetchLatestConfig()` was using `SyncEISConfigurationJob::dispatchNow()` which processed ALL businesses through the job queue instead of making a direct API request for the current business.

**Root Cause**: 
- getLatestConfig() method had no businessId parameter
- Was processing all configured businesses unnecessarily
- Added latency due to job queue processing

**Solution** (Updated two files):

#### a. Modules/EIS/Services/Terminal/EisTerminalActivationService.php (lines 931-985)
Changed `getLatestConfig()` method to:
- Accept `int $businessId` parameter
- Retrieve EIS settings for the specific business
- Call `ConfigurationSyncService::sync()` directly with that business's JWT token
- Update sync status for that business
- Create/update business location
- Return structured response with version information

#### b. Modules/EIS/Http/Controllers/API/TerminalActivationController.php (lines 299-323)
Updated `fetchLatestConfig()` controller method to:
- Extract `business_id` from request
- Pass it to `getLatestConfig($businessId)` service method
- Simplified response handling (removed job queue-specific logic)
- Consistent error response format

**Benefits**:
- Direct API call (no job queue overhead)
- Immediate response time
- Only syncs configurations for the requested business
- Consistent with other single-business operations

### 4. Terminal Status Preservation During Config Sync
**Issue**: Terminal status was being reset when `isActiveTerminal` field was missing from EIS API response.

**Root Cause**: The sync method defaulted `is_active = false` when the field was absent

**Solution** (Modules/EIS/Services/Configuration/ConfigurationSyncService.php, lines 393-417):
- Updated conditional logic: only update `is_active` if `isActiveTerminal` is explicitly provided
- Preserves existing terminal status when field is absent from response
- Safe default of `false` for newly created terminals
- Result: Active terminals remain active across configuration syncs

---

## Technical Details

### API Response Structure
- The EIS API endpoint `/configuration/get-latest-configs` returns optional fields
- `isActiveTerminal` is optional per ConfigurationValidator
- Previously: EISConfigurationResponse defaulted missing values to `false`, causing status reset
- Now: Conditional logic respects API contract while preserving existing state

### Business Location Creation
- Static method `ConfigurationSyncService::businessLocation($businessId)` called after successful sync
- Uses data from EIS configuration response (terminalSite, terminalConfig)
- Creates location with auto-generated default invoice scheme and layout if needed

### Database Schema Relationships
- `business_locations` requires foreign key to `invoice_schemes`
- `invoice_schemes` must be created before location can be inserted
- Both `invoice_scheme_id` and `invoice_layout_id` required on new locations

---

## Files Modified

1. **Modules/EIS/Services/Configuration/ConfigurationSyncService.php**
   - Fixed businessLocation() method (lines 1190-1237)
   - Fixed syncTerminalConfiguration() method (lines 393-417)

2. **Modules/EIS/Http/Controllers/API/TerminalActivationController.php**
   - Updated details() method to include branch information (line ~740)
   - Updated fetchLatestConfig() method to use direct API call (lines 299-323)

3. **Modules/EIS/Services/Terminal/EisTerminalActivationService.php**
   - Refactored getLatestConfig() to accept businessId and call API directly (lines 931-985)

---

## Testing Recommendations

1. **Terminal Activation Test**
   - Activate a new terminal for business ID 2 with activation code
   - Verify: No foreign key errors
   - Verify: business_locations record created with all required fields
   - Verify: Terminal status is correct

2. **Branch Display Test**
   - Navigate to EIS settings page (settings_mra_eis)
   - Verify: Branch field shows business location name, not "N/A"
   - Verify: Field updates after terminal activation

3. **Get Latest Config Test**
   - Send POST request to `/api/v1/terminal/get-latest-config` with `{"business_id": 2}`
   - Verify: Direct API response (no job queue processing)
   - Verify: Response includes version information
   - Verify: Sync status updated in database
   - Verify: Only requested business is synced (check logs)

4. **Terminal Status Preservation Test**
   - Activate and activate a terminal (is_active = true)
   - Call "get latest config" (API response lacks isActiveTerminal)
   - Verify: Terminal remains is_active = true in database

---

## Backward Compatibility

- All changes are backward compatible
- No database migrations required
- Existing API responses still valid
- Job queue (SyncEISConfigurationJob) still available for bulk syncing

---

## Related Documentation

- EIS API Response: Modules/EIS/Services/Configuration/EISConfigurationResponse.php
- Configuration Validation: Modules/EIS/Services/Configuration/ConfigurationValidator.php
- Terminal Configuration Model: Modules/EIS/Models/EisTerminalConfiguration.php
- Invoice Scheme Model: App/InvoiceScheme.php
