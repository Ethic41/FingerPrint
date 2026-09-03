# Troubleshooting

Work from the reader toward the server. Do not post fingerprint samples, database passwords or tokens in an issue.

## The reader list is empty

1. Confirm the U.are.U reader appears in Windows Device Manager.
2. Confirm the DigitalPersona driver and Web SDK runtime are installed and running.
3. Unplug and reconnect the reader after the runtime starts.
4. Open browser developer tools and check for `Fingerprint is not defined`, WebSocket errors or blocked mixed content.
5. Run `scripts/windows-preflight.ps1` in PowerShell.

## The page has no styling or buttons do nothing

Update to the compatibility release. Older revisions used `/src/...` URLs, which break when the repository is installed at `/FingerPrint` under XAMPP.

Check the browser Network panel for HTTP 404 responses.

## Enrollment returns a service error

From `fingerprint_service`, run:

```powershell
docker compose ps
docker compose logs fingerprint_engine fingerprint_client
Invoke-WebRequest http://127.0.0.1:5555/health.php
```

Then open `<application-url>/src/core/diagnostics.php`.

## MySQL connection fails

Confirm Apache and MySQL are running in XAMPP, import `database/schema.sql`, and configure `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` and `DB_PASSWORD`. Do not use a passwordless root account outside a local development computer.

## Information to include in a bug report

- Output of `winver`.
- Whether Docker Desktop uses WSL 2 or Hyper-V.
- Output of `wsl --version`, `docker version`, and `docker compose ps`.
- Redacted service logs.
- XAMPP and PHP versions.
- Exact application URL.
- Browser name/version and redacted console errors.
- Diagnostic endpoint output.
- Whether Device Manager detects the reader.
