# Terminal Activation Confirmation Flow

## Overview

According to the EIS API documentation (5.1.2 Terminal Activated Confirmation), after a terminal's configuration data is successfully saved locally, the system **must send a confirmation request to the EIS API**. This confirmation:

1. Flags the terminal as activated in the EIS system
2. Invalidates the Terminal Activation Code (TAC)
3. Signals that the terminal is ready for production use

## Activation Flow (Per EIS Documentation)

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Terminal Inputs Activation Code (TAC)                    │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Call /onboarding/activate-terminal                       │
│    - Validates TAC                                          │
│    - Returns config + terminal credentials                  │
│    - Status: "Terminal Activated, pending for confirmation" │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Store Configuration Locally                              │
│    - Save taxpayer info                                     │
│    - Save terminal credentials (JWT token + secret key)     │
│    - Save global/terminal/taxpayer configuration            │
│    - Status: Waiting to confirm with EIS                    │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Send /onboarding/terminal-activated-confirmation         │
│    ✓ CRITICAL STEP - Must be done for terminal to activate  │
│    - Uses JWT token from credentials                        │
│    - Signs request with X-Signature (activation code + key) │
│    - Confirms local storage was successful                  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. EIS Flags Terminal as Activated                          │
│    - TAC becomes invalid                                    │
│    - Terminal can now send sales data                       │
│    - Display "Terminal Successfully Activated"              │
└─────────────────────────────────────────────────────────────┘
```

## Implementation in Jazz

### Current Flow
1. **TerminalActivationController::activate()** - Entry point for activation
2. **EisTerminalActivationService::activateTerminal()** - Main orchestration
   - Step 1: Call `callActivationAPI()` → `/onboarding/activate-terminal`
   - Step 2: Call `processActivationResponse()` → Save to database
   - Step 3: Call `sendActivationConfirmation()` → `/onboarding/terminal-activated-confirmation`

### Key Method: `sendActivationConfirmation()`

Located in: `Modules/EIS/Services/Terminal/EisTerminalActivationService.php` (line ~106)

```php
private function sendActivationConfirmation(
    string $terminalId,
    string $activationCode,
    string $jwtToken,
    string $secretKey
): bool
```

#### Parameters
- **$terminalId** - Terminal ID from EIS API response
- **$activationCode** - Original Terminal Activation Code (TAC)
- **$jwtToken** - JWT token from terminal credentials
- **$secretKey** - Secret key from terminal credentials

#### Request Details
- **Endpoint**: `/onboarding/terminal-activated-confirmation`
- **Method**: POST
- **URL**: `{$apiBaseUrl}/onboarding/terminal-activated-confirmation`
- **Payload**: `{ "terminalId": "xxx-xxx-xxx" }`
- **Headers**:
  - `Authorization: Bearer {jwtToken}`
  - `X-Signature: {signature}` - Computed using HmacSHA512(activationCode, secretKey)
  - `accept: text/plain`

#### Signature Computation
```php
private function computeXSignature(string $activationCode, string $secretKey): string
{
    // Compute HMAC-SHA512(activationCode, secretKey)
    // Base64 encode the result
    $hash = hash_hmac('sha512', $activationCode, $secretKey, true);
    return base64_encode($hash);
}
```

#### Response Handling
- **Success**: Updates `eis_terminal_configurations` table:
  - `is_confirmed = true`
  - `confirmed_at = now()`
  - `confirmation_response = {json_response}`
- **Failure**: Logs error but doesn't block activation

#### Database Schema
```sql
ALTER TABLE eis_terminal_configurations ADD COLUMN confirmed_at TIMESTAMP;
ALTER TABLE eis_terminal_configurations ADD COLUMN confirmation_response LONGTEXT;
-- Also includes boolean: is_confirmed (default false)
```

## Current Status (After Fix)

### What Was Fixed
1. **Corrected method call** - Removed extra `$siteId` parameter that wasn't used
2. **Added null checks** - Verify all required credentials exist before sending
3. **Improved error handling** - Track confirmation success/failure in logs
4. **Better logging** - Shows when confirmation is sent and if it fails

### Confirmation Flow in `activateTerminal()`

```php
// Step 1: Activate terminal via API
$activationResponse = $this->callActivationAPI($businessId, $activationCode, $environment);

// Step 2: Process response and save to database
$result = $this->processActivationResponse(
    $businessId,
    $activationResponse,
    $activationCode,
    $environment,
    $activatedBy
);

// Step 3: Send confirmation to EIS
if ($result['success']) {
    $terminalId = $result['data']['terminal_id'];
    $secretKey = $result['terminal_credentials']['secretKey'];
    $jwtToken = $result['terminal_credentials']['jwtToken'];
    
    if ($terminalId && $secretKey && $jwtToken) {
        $confirmationSuccess = $this->sendActivationConfirmation(
            $terminalId,
            $activationCode,
            $jwtToken,
            $secretKey
        );
        
        // Log if confirmation failed
        if (!$confirmationSuccess) {
            Log::warning('Activation confirmation could not be sent to EIS', [
                'terminal_id' => $terminalId,
                'business_id' => $businessId
            ]);
        }
    }
}

// Step 4: Create business location
ConfigurationSyncService::businessLocation($businessId);

return $result;
```

## Important Notes

### ✓ What Happens Now
- Terminal is activated in Jazz database immediately
- Configuration is saved (taxpayer, terminal, global settings)
- JWT token and secret key are stored
- Activation confirmation is sent to EIS
- Terminal is marked as confirmed when EIS responds positively

### ⚠️ Important Behaviors
1. **Confirmation is best-effort**: If it fails, terminal remains activated locally but may not be fully activated in EIS
2. **Logs will show** if confirmation fails - check logs for debugging
3. **TAC becomes invalid** only after successful confirmation at EIS
4. **Terminal should be ready for sales** only after confirmation succeeds
5. **Frontend should verify** the confirmation status before allowing sales

### 🔐 Security Considerations
- X-Signature prevents tampering with confirmation request
- JWT token is used for authentication
- Secret key is never sent in the request, only used for signature computation
- Terminal credentials should be stored securely in database

## Testing Checklist

- [ ] Terminal activation initiates confirmation request
- [ ] Confirmation request includes correct terminalId
- [ ] X-Signature is computed correctly (HmacSHA512)
- [ ] JWT token is included in Authorization header
- [ ] Confirmation response is logged
- [ ] Database is updated with confirmation status
- [ ] Failure to send confirmation is logged (doesn't block activation)
- [ ] Terminal can send sales data after confirmation succeeds

## Logging

Check logs for:
```
Sending activation confirmation
  - url: /onboarding/terminal-activated-confirmation
  - terminal_id: xxx
  - signature: yyy
  - activationCode: zzz

Activation confirmation sent successfully
  - terminal_id: xxx
  - response: {...}

or

Activation confirmation failed
  - terminal_id: xxx
  - status: 400/401/500
  - response: error message
```

## Related Files

- **Main Service**: `Modules/EIS/Services/Terminal/EisTerminalActivationService.php`
- **Controller**: `Modules/EIS/Http/Controllers/API/TerminalActivationController.php`
- **Configuration Service**: `Modules/EIS/Services/Configuration/ConfigurationSyncService.php`
- **Terminal Model**: `Modules/EIS/Models/EisTerminalConfiguration.php`
- **Database Migrations**: `Modules/EIS/Database/Migrations/*terminal*.php`

## Summary

**Terminal Activation Confirmation** is a **required step** in the EIS onboarding process:

1. ✅ Validates terminal configuration was saved successfully
2. ✅ Marks terminal as ready in EIS system
3. ✅ Invalidates the activation code to prevent reuse
4. ✅ Enables terminal for production sales

The Jazz system now properly:
- ✅ Calls the confirmation endpoint after activation
- ✅ Includes all required headers (Authorization, X-Signature)
- ✅ Handles success and failure cases
- ✅ Updates database with confirmation status
- ✅ Provides detailed logging for troubleshooting

**Per EIS Documentation 5.1.2**: "If the terminal activated confirmation is not sent, the terminal will remain inactivated and the API will not be able to process any subsequent messages."
