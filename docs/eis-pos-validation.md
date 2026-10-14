# EIS buyer and relief validation

POS create and update validate before opening the sale's write transaction or
uploading its document. EIS must have active settings for the current business.
Ordinary sales for businesses without active EIS settings retain their existing
flow; relief sales require active settings and a buyer TIN.

The selected business-scoped contact supplies the buyer TIN and authorization
code. Request overrides do not replace that identity.

- `POST /api/v1/utilities/check-tin-authorization-requirement`: sends `tin`;
  confirms that the TIN exists and whether an authorization code is required.
- `POST /api/v1/utilities/validate-authorization-code`: sends only
  `authorizationCode`. Every supplied contact code is checked, including codes
  supplied without a TIN. This endpoint does not prove that a code belongs to a TIN.
- `POST /api/v1/utilities/validate-vat5-certificate`: sends `projectNumber`,
  `certificateNumber`, and numeric `quantity` for relief sales.

The utility client uses the business JWT and supports a configured base URL
ending either at the host or at `/api/v1`. HTTP failures, unavailable EIS,
malformed envelopes, response identity mismatches, and non-boolean/false validity
flags block saving. No validation credentials, codes, certificates or response
bodies are logged by the new services.

The relief checkbox reveals project number, certificate number, and positive
quantity inputs. Successful validation stores those details as JSON on the sale;
the EIS header includes them only when `isReliefSupply` is true. No certificate
ID is invented. Unchecking relief clears the stored certificate. This feature
does not change existing tax-rate calculations.

Payment-only edits ignore submitted buyer/relief metadata and validate the saved
identity and certificate. General edits preserve omitted certificate fields only
for the same buyer. A locked-boundary snapshot check rejects concurrent changes
to the existing buyer, relief flag, or certificate. Existing saved-sale split
fences remain in place.

Storage migration: `2026_10_14_000003_add_vat5_certificate_details_to_transactions_table`.
The additive migration was applied locally. Other environments must deploy it
before using the updated sale utility.

### Browser migration deployment

Open `/admin/eis-migrations` while signed in with the `manage_modules`
permission, or use **Manage Modules → EIS Database Migrations**. Review the
applied/pending list, back up the database, stop sales/payment activity, and
confirm before pressing **Run EIS Migrations**. Merely opening the page does not
run migrations. The CSRF-protected POST runs only the three listed additive
migrations, with a shared file-cache lock and rate limiting. It does not run
other pending migrations, reset the database, seed data, or enable debug mode.
Demo access is disabled. Existing migrations are skipped. On failure, some
earlier migrations may have completed; review deployment/logs before retrying.

`tests/Standalone/browser_eis_migration_checks.php` covers privileged access,
confirmation, exact command scope, lock contention/release, error handling, and
POST/CSRF routing using mocked commands only.

Standalone validation fixture: `tests/Standalone/eis_pos_validation_checks.php`.
108 checks use SQLite memory and HTTP fakes, including contracts, failures,
tenant isolation, actual create/update rejection, persistence, header mapping,
concurrent-state rejection, and rendered edit/payment-only controls. No live EIS
validation has been performed.

## Payment completion

Adding or editing an invoice payment initiates EIS submission when an unpaid or
partially paid final sale becomes `paid` (remaining balance zero). Customer-due
payments do the same for each fully settled invoice after child payments are
inserted. Submission uses the existing `SaleCompleted` / `eis-sales` queue flow
after the outer database transaction commits. Partial payments, purchases,
returns and draft sales do not initiate submission; rollback discards callbacks.
The EIS queue worker must be running. The hourly unsubmitted-sales job remains
the fallback. Existing fiscal submission deduplication and retry behavior applies.

`tests/Standalone/eis_added_payment_checks.php` executes 52 isolated checks of
the real add/edit payment controllers, customer-due allocation, after-commit
timing, paid retries, partial exclusions and rollback safety. No EIS HTTP calls
or live payments are made by this fixture.