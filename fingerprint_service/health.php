<?php
header('Content-Type: application/json; charset=utf-8');

$grpcLoaded = extension_loaded('grpc');
$engine = @fsockopen('fingerprint_engine', 4134, $errorNumber, $errorMessage, 2);
$engineReachable = is_resource($engine);
if ($engineReachable) {
    fclose($engine);
}

$ok = $grpcLoaded && $engineReachable;
http_response_code($ok ? 200 : 503);
echo json_encode([
    'ok' => $ok,
    'grpc_extension' => $grpcLoaded,
    'engine_reachable' => $engineReachable,
]);
