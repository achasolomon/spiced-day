<?php
/**
 * Cache Clearing Script for Shared Hosting
 * 
 * Run this file once via browser: https://spiced.algosoftwarelabs.com/clear-cache.php
 * Then DELETE this file for security reasons!
 */

// Security: Only allow if accessed directly (not included)
if (basename($_SERVER['PHP_SELF']) !== 'clear-cache.php') {
    die('Access denied');
}

// Simple password protection (change this password!)
$password = 'clear123'; // CHANGE THIS PASSWORD!

if (!isset($_GET['key']) || $_GET['key'] !== $password) {
    die('Access denied. Use: ?key=YOUR_PASSWORD');
}

echo "<h2>Clearing Laravel Caches...</h2>";
echo "<pre>";

$basePath = dirname(__DIR__);

// Clear route cache
$routeCache = $basePath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "✓ Route cache cleared\n";
} else {
    echo "✗ Route cache file not found\n";
}

// Clear config cache
$configCache = $basePath . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "✓ Config cache cleared\n";
} else {
    echo "✗ Config cache file not found\n";
}

// Clear view cache
$viewCacheDir = $basePath . '/storage/framework/views';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✓ View cache cleared\n";
} else {
    echo "✗ View cache directory not found\n";
}

// Clear compiled cache
$compiledCache = $basePath . '/bootstrap/cache/services.php';
if (file_exists($compiledCache)) {
    unlink($compiledCache);
    echo "✓ Compiled cache cleared\n";
} else {
    echo "✗ Compiled cache file not found\n";
}

echo "\n✅ All caches cleared successfully!\n";
echo "\n⚠️ IMPORTANT: Delete this file (clear-cache.php) now for security!\n";
echo "</pre>";

