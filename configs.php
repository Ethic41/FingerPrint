<?php
/**
 * Application configuration.
 *
 * Values are read from the environment so the same code works with XAMPP,
 * Docker and a hosted PHP installation. Defaults preserve the original local
 * XAMPP behaviour.
 */

if (!function_exists('fingerprint_config')) {
    function fingerprint_config($name, $default = null)
    {
        $value = getenv($name);
        return ($value === false || $value === '') ? $default : $value;
    }
}

// XAMPP does not provide a convenient per-project environment file. A local,
// ignored PHP file may set values with putenv(); see configs.local.php.example.
$localConfig = __DIR__ . '/configs.local.php';
if (is_file($localConfig)) {
    require $localConfig;
}
