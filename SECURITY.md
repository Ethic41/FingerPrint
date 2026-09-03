# Security

This repository is a proof of concept and must not be deployed as a production
biometric identity system without a security review.

## Reporting a vulnerability

Open a GitHub security advisory for vulnerabilities. Do not post credentials,
fingerprint samples, FMD templates, personal data, or exploitable details in a
public issue.

## Deployment baseline

- Serve the browser application over HTTPS outside localhost.
- Keep ports 4134 and 5555 on loopback or a private authenticated network.
- Authenticate users and authorize enrollment and verification separately.
- Add CSRF protection, request-size limits and rate limiting to PHP endpoints.
- Encrypt biometric templates at rest with managed, rotated keys.
- Never log request bodies containing FMD data.
- Use a dedicated least-privileged MySQL account, not `root`.
- Define retention, deletion, consent and audit policies appropriate to the
  jurisdiction in which biometric data is processed.

The provided Compose configuration binds service ports to `127.0.0.1` by
default. Operators who deliberately expose them are responsible for adding TLS,
authentication and network policy first.
