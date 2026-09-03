<?php
/**
 * Non-sensitive compatibility checks for XAMPP and hosted installations.
 */

header('Content-Type: application/json; charset=utf-8');

require_once(__DIR__ . '/../../configs.php');

if (fingerprint_config('DIAGNOSTICS_ENABLED', '1') !== '1') {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Diagnostics are disabled.']);
    exit;
}

$checks = [];
$checks['php'] = [
    'ok' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'version' => PHP_VERSION,
    'message' => version_compare(PHP_VERSION, '7.4.0', '>=')
        ? 'PHP runtime is available.'
        : 'PHP 7.4 or newer is required; a currently supported PHP release is recommended.',
];

$checks['mysqli'] = [
    'ok' => extension_loaded('mysqli'),
    'message' => extension_loaded('mysqli')
        ? 'mysqli is enabled.'
        : 'Enable the mysqli extension in php.ini and restart Apache.',
];

$checks['database'] = ['ok' => false, 'message' => 'Database check was not run.'];
if ($checks['mysqli']['ok']) {
    try {
        require_once(__DIR__ . '/Database.php');
        $database = new \fingerprint\Database();
        $checks['database'] = [
            'ok' => $database->ping(),
            'message' => 'MySQL connection succeeded.',
        ];
    } catch (Throwable $exception) {
        $checks['database'] = [
            'ok' => false,
            'message' => 'MySQL connection failed. Check DB_HOST, DB_PORT, DB_NAME, DB_USER and DB_PASSWORD.',
        ];
    }
}

$serviceUrl = fingerprint_config('FP_CLIENT_SERVICE_HOST', 'http://127.0.0.1:5555');
$parts = parse_url($serviceUrl);
$serviceHost = $parts['host'] ?? null;
$servicePort = $parts['port'] ?? (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);
$serviceOk = false;

if ($serviceHost) {
    $socket = @fsockopen($serviceHost, $servicePort, $errorNumber, $errorMessage, 3);
    if (is_resource($socket)) {
        $serviceOk = true;
        fclose($socket);
    }
}

$checks['fingerprint_service'] = [
    'ok' => $serviceOk,
    'target' => sprintf('%s:%d', $serviceHost ?: 'invalid-host', $servicePort),
    'message' => $serviceOk
        ? 'Fingerprint service port is reachable.'
        : 'Fingerprint service is unreachable. Check FP_CLIENT_SERVICE_HOST and Docker/service status.',
];

$allOk = !in_array(false, array_map(function ($check) {
    return $check['ok'];
}, $checks), true);

http_response_code($allOk ? 200 : 503);
echo json_encode([
    'ok' => $allOk,
    'checks' => $checks,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
