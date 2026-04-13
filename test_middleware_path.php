<?php

$paths = [
    '/login',
    '/admin/login052205',
    '/logout',
    '/student/dashboard',
    '/admin/dashboard',
    '/forgot-password',
    '/reset-password/token123',
    '/api/maintenance-status',
];

echo "=== UPDATED MIDDLEWARE CHECK (guest, maintenance ON) ===\n";
foreach ($paths as $p) {
    $path = ltrim($p, '/');
    $allowed = (
        $path === 'admin/login052205' ||
        $path === 'logout' ||
        str_starts_with($path, 'logout/') ||
        $path === 'api/maintenance-status'
    );
    echo str_pad("'$p'", 35).($allowed ? 'ALLOWED ✓' : 'BLOCKED ✓')."\n";
}
echo "\nDone.\n";
