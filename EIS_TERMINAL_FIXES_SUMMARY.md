# EIS Terminal Activation and Configuration Fixes

## Summary
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
