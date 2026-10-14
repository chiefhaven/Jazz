<?php

namespace Modules\EIS\Services\Sales;

use App\Contact;
use App\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Utilities\EisValidationClient;

class PosBuyerValidationService
{
    public function __construct(private EisValidationClient $client) {}

    /** Returns only server-validated certificate details; never accepts a client validity flag. */
    public function validate(int $businessId, array $input, ?Transaction $existing = null): ?array
    {
        if ($existing && (int) $existing->business_id !== $businessId) {
            throw ValidationException::withMessages(['contact_id' => 'Sale does not belong to this business.']);
        }
        $relief = array_key_exists('is_relief', $input)
            ? filter_var($input['is_relief'], FILTER_VALIDATE_BOOLEAN) : (bool) ($existing->is_relief ?? false);
        $contact = Contact::where('business_id', $businessId)->find($input['contact_id'] ?? $existing->contact_id ?? null);
        if (! $contact) {
            throw ValidationException::withMessages(['contact_id' => 'Select a buyer from this business.']);
        }
        // Use the same buyer identity that SaleTransformer sends, not arbitrary request overrides.
        $tin = trim((string) $contact->tax_number);
        $code = trim((string) $contact->authorization_code);
        $settings = EisSetting::where('business_id', $businessId)->where('status', true)->first();
        if (! $settings) {
            if ($relief) {
                throw ValidationException::withMessages(['is_relief' => 'Relief sales require active EIS settings.']);
            }
            return null; // Non-EIS businesses retain their ordinary checkout flow.
        }
        $certificate = null;
        if ($relief) {
            if ($tin === '') {
                throw ValidationException::withMessages(['buyerTIN' => 'A buyer TIN is required for a relief sale.']);
            }
            // Do not silently reuse another buyer's certificate when changing the customer.
            $stored = $existing && (int) $contact->id === (int) $existing->contact_id
                ? ($existing->vat5_certificate_details ?? []) : [];
            $certificate = [
                'projectNumber' => $input['vat5_project_number'] ?? $stored['projectNumber'] ?? null,
                'certificateNumber' => $input['vat5_certificate_number'] ?? $stored['certificateNumber'] ?? null,
                'quantity' => $input['vat5_quantity'] ?? $stored['quantity'] ?? null,
            ];
            Validator::make($certificate, [
                'projectNumber' => 'required|string|max:255',
                'certificateNumber' => 'required|string|max:255',
                'quantity' => 'required|numeric|gt:0',
            ])->validate();
            $certificate['quantity'] = (float) $certificate['quantity'];
        }
        if ($tin !== '') {
            $requirement = $this->client->checkTinAuthorizationRequirement($settings, $tin);
            if (($requirement['tinExists'] ?? null) !== true
                || ! is_bool($requirement['requiresAuthorizationCode'] ?? null)
                || ($requirement['tin'] ?? null) !== $tin) {
                throw ValidationException::withMessages(['buyerTIN' => 'EIS could not confirm the buyer TIN.']);
            }
            if ($requirement['requiresAuthorizationCode'] && $code === '') {
                throw ValidationException::withMessages(['buyerAuthorizationCode' => 'This buyer TIN requires an EIS authorization code. Edit the customer to add it.']);
            }
        }
        if ($code !== '') {
            $this->client->validateAuthorizationCode($settings, $code);
        }
        if ($certificate !== null) {
            $this->client->validateVat5Certificate($settings, $certificate);
        }
        return $certificate;
    }

    public function assertSaleUnchanged(Transaction $validated, Transaction $locked): void
    {
        if ((int) $validated->contact_id !== (int) $locked->contact_id
            || (bool) $validated->is_relief !== (bool) $locked->is_relief
            || $validated->vat5_certificate_details !== $locked->vat5_certificate_details) {
            throw ValidationException::withMessages(['is_relief' => 'The buyer or relief details changed during validation. Reload the sale and retry.']);
        }
    }
}