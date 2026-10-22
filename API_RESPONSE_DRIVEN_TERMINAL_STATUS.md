# Terminal Status Follows API Response - Final Fix

## Problem Statement
The terminal status must ALWAYS be driven by what the EIS API returns, not by preserving old database values when the API doesn't provide the field.

## Previous Incorrect Behavior

### Old Logic (WRONG):
```php
if (isset($terminalData->isActiveTerminal)) {
    $terminalConfigData['is_active'] = $terminalData->isActiveTerminal;
} elseif (!$existingTerminal) {
    $terminalConfigData['is_active'] = false;
} else {
    // Don't include is_active - preserve existing value
    // PROBLEM: This contradicts the API contract!
}
```

**Issue**: When API didn't provide `isActiveTerminal`, we were preserving the old status instead of following the API's intended behavior.

## Correct Behavior (NEW)

### New Logic (CORRECT):
```php
// Terminal status ALWAYS follows what API says
// If API doesn't provide isActiveTerminal, default to false (API contract)
'is_active' => $terminalData->isActiveTerminal ?? false
```

**Principle**: The terminal status is SOURCE OF TRUTH from the EIS API response, NOT the database.

## Why This Matters

### API Contract
The `EISConfigurationResponse` has this method:
```php
public function isTerminalActive(): bool
{
    return $terminalConfig->isActiveTerminal ?? false;
}
```

This tells us: **If the API doesn't provide `isActiveTerminal`, the correct value is `false`** (not active).

### Data Flow

#### OLD (WRONG):
```
API Response: { isActiveTerminal: undefined }
  ↓ (interpreted as)
Database: keep old status (could be true or false)
  ↓ (result)
Frontend: Shows whatever was in database, not what API says
  ✗ Disconnected from API truth
```

#### NEW (CORRECT):
```
API Response: { isActiveTerminal: undefined }
  ↓ (interpreted as)
is_active: false (API contract default)
  ↓ (result)
Database: false
  ↓ (result)
Frontend: Shows Inactive (what API says)
  ✓ Always in sync with API
```

## Implementation Details

**File**: `Modules/EIS/Services/Configuration/ConfigurationSyncService.php`
**Method**: `syncTerminalConfiguration()` (line ~390)

### Changes Made

1. **Removed conditional logic** that tried to preserve old status
2. **Always include `is_active` in terminalConfigData** with API value or false
3. **Let updateOrCreate() always update the field** because we now have a definitive value
4. **Updated logging** to show the actual is_active value being set

### Key Code Change
```php
// Terminal status comes from API response or defaults to false
'is_active' => $terminalData->isActiveTerminal ?? false
```

This single line ensures:
- ✓ If API provides isActiveTerminal: use that value
- ✓ If API doesn't provide isActiveTerminal: use false (API contract default)
- ✓ Database is always in sync with API
- ✓ No confusion from cached/old values

## Scenarios

### Scenario 1: API Says Terminal is Active
```
API: { isActiveTerminal: true }
Result: is_active = true
Display: Active ✓
```

### Scenario 2: API Says Terminal is Inactive
```
API: { isActiveTerminal: false }
Result: is_active = false
Display: Inactive ✓
```

### Scenario 3: API Doesn't Provide Status (Missing Field)
```
API: { /* isActiveTerminal not provided */ }
Result: is_active = false (API contract default)
Display: Inactive ✓
Frontend: Never shows stale status from database ✓
```

### Scenario 4: Get Latest Config with Partial Response
```
API: { global_version: "1.0", isActiveTerminal: undefined }
Result: is_active = false (API contract)
Database: Updated with is_active = false
Frontend: Shows Inactive (API truth)
Expected: Not showing stale Active status ✓
```

## Testing

### Test 1: Verify API-Driven Status
1. Terminal is Active in database
2. Call sync with API response missing `isActiveTerminal`
3. **Check database**: is_active should be false (not preserved)
4. **Check frontend**: Should show Inactive
5. **Verify logs**: Should log `is_active: false`

### Test 2: Active Terminal Stays Active
1. API returns `{ isActiveTerminal: true }`
2. Call sync
3. **Check database**: is_active = true
4. **Check frontend**: Shows Active ✓

### Test 3: Inactive Terminal Stays Inactive
1. API returns `{ isActiveTerminal: false }`
2. Call sync
3. **Check database**: is_active = false
4. **Check frontend**: Shows Inactive ✓

### Test 4: Get Latest Config
1. Click "Get Latest Config" button
2. **Check response**: Includes terminal_is_active (whatever API determined)
3. **Check frontend**: Displays correct status based on API response
4. **Verify**: No false status changes

## Related Files

- **Configuration Sync**: `Modules/EIS/Services/Configuration/ConfigurationSyncService.php`
- **API Response Model**: `Modules/EIS/Services/Configuration/EISConfigurationResponse.php`
- **Terminal Activation Service**: `Modules/EIS/Services/Terminal/EisTerminalActivationService.php`
- **Frontend**: `resources/views/business/settings.blade.php`

## Key Takeaway

**Terminal status is not a local decision - it's driven by the EIS API response.**

When syncing configuration:
- Always set is_active to: `$terminalData->isActiveTerminal ?? false`
- Never preserve old status
- Always trust the API response
- This ensures database and frontend are never out of sync with API truth

## Backward Compatibility

✓ All existing code continues to work
✓ Only changes the logic for when API doesn't provide isActiveTerminal
✓ Frontend already handles the corrected status
✓ No breaking changes to API contracts
