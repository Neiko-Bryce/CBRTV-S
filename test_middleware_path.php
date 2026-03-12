<?php
$paths = [
    '/login',
    '/logout',
    '/student/dashboard',
    '/admin/dashboard',
    '/forgot-password',
    '/reset-password/token123',
    '/api/maintenance-status',
];

echo "=== UPDATED MIDDLEWARE CHECK ===\n";
foreach ($paths as $p) {
    $path = ltrim($p, '/');
    $allowed = (
        $path === 'login' ||
        $path === 'logout' ||
        str_starts_with($path, 'login/')
    );
    echo str_pad("'$p'", 35) . ($allowed ? 'ALLOWED ✓' : 'BLOCKED ✓') . "\n";
}
echo "\nDone.\n";
