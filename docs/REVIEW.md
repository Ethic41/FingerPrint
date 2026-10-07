# Second compatibility review

This branch remains a proof of concept, not a production biometric identity
system. Hardware is unavailable, so CI deliberately separates reader-independent
tests from claims about DigitalPersona device compatibility.

## Fixes made during the second pass

- Missing SDK no longer crashes before initialization diagnostics run.
- Browser form encoding preserves captured JSON without corrupting reserved characters.
- Capture targets the selected reader instead of ignoring its ID.
- Enrollment and verification reject missing samples before sending requests.
- Profile fields use DOM properties rather than interpolated HTML.
- PHP validates method, payload size, user ID, sample count and URL-safe encoding.
- Enrollment validates engine output before storing templates.
- Duplicate detection fails closed on malformed or unavailable service responses.
- Missing database records do not produce null dereferences.
- gRPC calls have explicit deadlines and duplicate checks validate status.
- Diagnostics default to disabled and unexpected errors do not expose exception details.
- Native SDK calls are serialized; enrollment cleanup uses scope-based ownership.
- RPC response objects are owned and freed instead of leaking on every call.
- Base64 accumulation uses unsigned arithmetic to avoid signed-shift undefined behavior.
- The PowerShell conditional expression was repaired and receives parser validation in CI.

## Reader-independent verification

CI runs PHP validation tests on PHP 8.2, 8.3 and 8.4; mocked browser tests;
Compose validation; PowerShell parsing; and native Base64 round-trip tests with
AddressSanitizer and UndefinedBehaviorSanitizer. These tests do not substitute
for compiling and running the complete proprietary matching engine.

## Remaining release gates

1. Build both container images against the actual legacy base images and SDK library.
2. Audit and lock the PHP service dependencies. The top-level service still has no
   Composer lock file; `composer install` alone does not make that build reproducible.
3. Run hardware capture, enrollment and positive/negative matching on Windows.
4. Verify browser-to-local-runtime connectivity from an HTTPS hosted application.
5. Before production, implement application authentication, authorization, CSRF
   protection, template encryption, rate limits and transactional enrollment.
6. Extend the native engine to distinguish invalid FMD/SDK failure from a genuine
   non-match, and validate malformed input through the full gRPC service.

Do not merge on the basis of green syntax checks alone. The second-pass regression
tests increase confidence in web behavior and validation, but SDK integration and
production security remain explicit limitations.
