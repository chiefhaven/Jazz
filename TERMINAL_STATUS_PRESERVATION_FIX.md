# Terminal Status Preservation Fix - Get Latest Config

## Problem
When clicking "Get Latest Config" button, the terminal was being displayed as "Inactive" even though it was actually "Active". The issue occurred because the frontend JavaScript was updating the terminal status display to Inactive when it shouldn't.

## Root Cause Analysis

### Issue 1: Missing terminal_is_active in API Response
The `getLatestConfig()` method was returning only version information:
```json
{
  "success": true,
  "data": {
    "global_version": "1.0",
    "terminal_version": "2.0",
    "taxpayer_version": "3.0"
  }
}
```

It did NOT include the terminal's actual `is_active` status after the sync.

### Issue 2: JavaScript Treats Undefined as False
The `updateTerminalUI()` JavaScript function was checking `if (data.is_active)` without checking if the field was explicitly provided:

```javascript
if (data.is_active) {  // undefined evaluates to false!
    // show Active
} else {
    // show Inactive  ← ALWAYS EXECUTED when is_active is undefined
}
```

When `data.is_active` was undefined (not in response), JavaScript treated it as false, so the UI always showed "Inactive".

### Data Flow Problem
```
Backend: sync() updates/preserves is_active ✓
         but doesn't return it in response ✗
         
Frontend: receives data without is_active
          JavaScript shows "Inactive" (because undefined == false) ✗
          User thinks terminal was deactivated
```

## Solution Implemented

### Backend Change (EisTerminalActivationService.php)

**Updated `getLatestConfig()` to include terminal status in response:**

```php
// Get the updated terminal details after sync
$terminal = EisTerminalConfiguration::where('configuration_id', $configuration->id)->first();

return [
    'success' => true,
    'message' => 'Latest configuration fetched successfully',
    'data' => [
        'global_version' => $configuration->global_version,
        'terminal_version' => $configuration->terminal_version,
        'taxpayer_version' => $configuration->taxpayer_version,
        'terminal_is_active' => $terminal?->is_active ?? false  // NEW FIELD
    ]
];
```

Now the response includes the preserved terminal status:
```json
{
  "success": true,
  "data": {
    "global_version": "1.0",
    "terminal_version": "2.0",
    "taxpayer_version": "3.0",
    "terminal_is_active": true
  }
}
```

### Frontend Change (settings.blade.php)

**Updated `updateTerminalUI()` to check for explicit field presence:**

```javascript
// Only update status if is_active is explicitly provided (from status/details endpoint)
if (typeof data.is_active !== 'undefined') {
    if (data.is_active) {
        // Show Active
    } else {
        // Show Inactive
    }
} else if (typeof data.terminal_is_active !== 'undefined') {
    // Handle response from "Get Latest Config" which returns terminal_is_active
    if (data.terminal_is_active) {
        // Show Active
    } else {
        // Show Inactive
    }
}

// Update detail fields only if they exist in the response
if (data.terminal_label) {
    $('#terminal_label_display').text(data.terminal_label || 'N/A');
}
// ... similar checks for other fields
```

**Key Improvements:**
1. ✅ Explicitly check if field is `undefined` before treating as false
2. ✅ Support both `is_active` (from status endpoint) and `terminal_is_active` (from get latest config)
3. ✅ Only update UI fields that are actually present in the response
4. ✅ Prevents accidental status changes from partial response data

## How It Works Now

### Scenario: Click "Get Latest Config" while terminal is Active

**Backend:**
1. Calls `sync()` which fetches latest config from EIS API
2. EIS API doesn't provide `isActiveTerminal` field
3. ConfigurationSyncService preserves existing `is_active = true` (doesn't overwrite)
4. Method returns the preserved status: `terminal_is_active: true`

**Frontend:**
1. Receives response with `terminal_is_active: true`
2. JavaScript explicitly checks: `typeof data.terminal_is_active !== 'undefined'` ✓
3. Sees value is `true` ✓
4. Displays "Active" badge ✓
5. Shows active action buttons ✓

## Impact

✅ **Backend**: Terminal status correctly preserved during sync
✅ **API Response**: Now includes terminal_is_active field
✅ **Frontend**: Correctly displays preserved status
✅ **UI Actions**: Active terminals remain active after config sync
✅ **User Experience**: No confusing status changes

## Files Modified

1. **Modules/EIS/Services/Terminal/EisTerminalActivationService.php**
   - Updated `getLatestConfig()` method (lines 931-990)
   - Now retrieves updated terminal after sync
   - Returns `terminal_is_active` in response data
   - Includes logging of terminal status

2. **resources/views/business/settings.blade.php**
   - Updated `updateTerminalUI()` function (lines 569-625)
   - Added explicit `typeof` checks for field presence
   - Supports both `is_active` and `terminal_is_active` fields
   - Only updates UI fields that exist in response

## Testing Recommendations

### Test 1: Get Latest Config on Active Terminal
1. Navigate to Business → Settings → EIS Tab
2. Verify terminal shows as "Active"
3. Click "Get Latest Config" button
4. **Expected**: Terminal still shows "Active" (not deactivated)
5. **Verify**: Response includes `terminal_is_active: true`

### Test 2: Get Latest Config on Inactive Terminal
1. Deactivate terminal first
2. Verify it shows as "Inactive"
3. Click "Get Latest Config" button
4. **Expected**: Terminal still shows "Inactive" (not changed)
5. **Verify**: Response includes `terminal_is_active: false`

### Test 3: Page Reload After Config Sync
1. Get latest config on active terminal
2. Refresh the page
3. **Expected**: Terminal still shows as "Active"
4. **Verify**: Status call shows correct state

### Test 4: Browser Console Check
1. Open browser developer tools (F12)
2. Get latest config
3. Check Network tab → response JSON
4. **Verify**: Response includes `"terminal_is_active": true/false`

## Related Components

- **Terminal Status Sync**: `ConfigurationSyncService::syncTerminalConfiguration()` - Preserves is_active status
- **Status Endpoint**: `/api/v1/terminal/status/{businessId}` - Returns full terminal details
- **Details Endpoint**: `/api/v1/terminal/details/{businessId}` - Returns full terminal details
- **Get Latest Config**: `/api/v1/terminal/get-latest-config` - Returns config data with status

## Backward Compatibility

✅ Changes are backward compatible:
- Old responses without `terminal_is_active` still work (field is optional)
- `updateTerminalUI()` gracefully handles missing fields
- Endpoints still return all existing fields

## References

- EIS Configuration Sync: `Modules/EIS/Services/Configuration/ConfigurationSyncService.php`
- Terminal Activation Service: `Modules/EIS/Services/Terminal/EisTerminalActivationService.php`
- Terminal Configuration Model: `Modules/EIS/Models/EisTerminalConfiguration.php`
- EIS Settings View: `resources/views/business/partials/settings_mra_eis.blade.php`
