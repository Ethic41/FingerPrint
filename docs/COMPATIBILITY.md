# Compatibility and deployment models

The application has three independent runtime layers:

1. The DigitalPersona driver and Web SDK service run on the Windows computer connected to the reader.
2. The PHP application stores users and Fingerprint Minutiae Data (FMD) in MySQL.
3. The Linux fingerprint service performs enrollment and matching through the DigitalPersona engine.

MySQL and PHP do not communicate with the USB reader directly.

## Supported configurations

| Profile | Browser and reader | PHP/MySQL | Fingerprint service | Status |
| --- | --- | --- | --- | --- |
| `xampp-local` | Windows 10/11 | XAMPP on the same PC | Docker Desktop on the same PC | Recommended for evaluation |
| `hosted` | Windows 10/11 client | Remote PHP/MySQL server | Private service beside the PHP server | Recommended for deployment |
| `local-docker` | Windows 10/11 | Existing local PHP/MySQL | Local Docker Desktop | Supported |
| `shared-hosting` | Windows client | Shared PHP host | Separate HTTPS service | Conditional on outbound network access |
| `xampp-no-docker` | Windows | XAMPP | Native Windows matching sidecar | Not implemented |

## Important meaning of localhost

`FP_CLIENT_SERVICE_HOST=http://127.0.0.1:5555` is correct only when PHP and the Docker service run on the same computer. In a hosted installation, `127.0.0.1` is the hosting server. It never refers to the Windows computer viewing the page.

For hosted deployments, run the fingerprint service in the same private network as PHP and set `FP_CLIENT_SERVICE_HOST` to its private authenticated address. Do not expose the raw gRPC port to the Internet.

## Windows baseline

- 64-bit Windows 10 22H2 (build 19045) or a supported Windows 11 release.
- WSL 2.1.5 or later, or a working Hyper-V configuration.
- Hardware virtualization enabled in BIOS/UEFI.
- Docker Desktop running Linux containers.
- DigitalPersona U.are.U driver, SDK and Web SDK local service installed.
- A supported browser able to communicate with the local Web SDK service.

Windows Pro alone does not establish any of these prerequisites.

## Local XAMPP setup

1. Install the DigitalPersona driver/SDK and restart Windows.
2. Install Docker Desktop and select Linux containers with the WSL 2 backend.
3. Copy the repository to `C:\xampp\htdocs\FingerPrint`.
4. Copy `configs.local.php.example` to `configs.local.php` and adjust values.
5. Import `database/schema.sql` in phpMyAdmin or the MySQL CLI.
6. From `fingerprint_service`, run `docker compose up -d --build`.
7. Run `powershell -ExecutionPolicy Bypass -File scripts/windows-preflight.ps1`.
8. Open `http://localhost/FingerPrint/src/core/diagnostics.php`.
9. Open `http://localhost/FingerPrint/` and test reader enumeration before enrollment.

## Hosted setup

1. Install the reader driver and Web SDK on every Windows capture workstation.
2. Serve the application over HTTPS.
3. Configure database credentials through server environment variables.
4. Deploy the fingerprint service beside the PHP server, not on the capture workstation.
5. Configure `FP_CLIENT_SERVICE_HOST` with the service's private URL.
6. Apply authentication, request-size limits, rate limits and TLS between services.
7. Encrypt stored biometric templates and ensure application logs never contain FMD values.

## Diagnostic endpoint

`src/core/diagnostics.php` tests the PHP version, `mysqli`, database connection and TCP reachability of the configured fingerprint service. It deliberately does not return passwords, full connection strings or exception traces.

Set `DIAGNOSTICS_ENABLED=0` after production commissioning if this endpoint is
not protected by the application's administrator authorization.

Browser capture remains a client-side check. Use the browser console and the Windows preflight script to distinguish missing JavaScript, a stopped DigitalPersona service and an undetected reader.

## CPU architecture

The bundled DigitalPersona Linux engine library is currently an `amd64`
dependency. Compose declares `linux/amd64` explicitly so Windows x86-64 uses
the correct image. ARM64 support requires an ARM64 build of the proprietary
DigitalPersona engine library; emulation should not be presented as native
support without scanner and matching tests.
