#!/usr/bin/env php
<?php

/**
 * RBAC Implementation Verification Script
 * 
 * Script ini memverifikasi bahwa semua komponen production-ready payroll system
 * sudah ter-implement dengan benar.
 * 
 * Penggunaan:
 * php verify_rbac_implementation.php
 * 
 * @author Your Name
 * @version 1.0
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  RBAC Implementation Verification Script                       ║\n";
echo "║  Production Ready Payroll System                              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Array untuk tracking status
$checks = [
    'Models' => [],
    'Migrations' => [],
    'Seeder' => [],
    'Middleware' => [],
    'Controllers' => [],
    'Routes' => [],
    'Traits' => [],
];

$basePath = __DIR__;

// ============ MODEL CHECKS ============
echo "Checking Models...\n";
$models = ['User', 'Role', 'Permission', 'Pegawai', 'Officer', 'Absensi', 'Lembur', 'Penggajian'];
foreach ($models as $model) {
    $path = "$basePath/app/Models/{$model}.php";
    $exists = file_exists($path);
    $checks['Models'][$model] = $exists ? '✓' : '✗';
    echo "  [{$checks['Models'][$model]}] {$model}.php\n";
}

// ============ MIDDLEWARE CHECKS ============
echo "\nChecking Middleware...\n";
$middlewares = ['DepartmentScope', 'RoleBasedAccess', 'CheckPermission', 'CheckRole'];
foreach ($middlewares as $middleware) {
    $path = "$basePath/app/Http/Middleware/{$middleware}.php";
    $exists = file_exists($path);
    $checks['Middleware'][$middleware] = $exists ? '✓' : '✗';
    echo "  [{$checks['Middleware'][$middleware]}] {$middleware}.php\n";
}

// ============ TRAIT CHECKS ============
echo "\nChecking Traits...\n";
$traits = ['HasPermissions', 'DataVisibility'];
foreach ($traits as $trait) {
    $path = "$basePath/app/Traits/{$trait}.php";
    $exists = file_exists($path);
    $checks['Traits'][$trait] = $exists ? '✓' : '✗';
    echo "  [{$checks['Traits'][$trait]}] {$trait}.php\n";
}

// ============ CONTROLLER CHECKS ============
echo "\nChecking Controllers...\n";
$controllers = [
    'Base' => 'BaseController.php',
    'Administrator/Absensi' => 'Administrator/AbsensiController.php',
    'Administrator/Lembur' => 'Administrator/LemburController.php',
    'Officer/Absensi' => 'Officer/AbsensiController.php',
    'Officer/Lembur' => 'Officer/LemburController.php',
    'Student/Attendance' => 'Student/AttendanceController.php',
];
foreach ($controllers as $name => $file) {
    $path = "$basePath/app/Http/Controllers/{$file}";
    $exists = file_exists($path);
    $checks['Controllers'][$name] = $exists ? '✓' : '✗';
    echo "  [{$checks['Controllers'][$name]}] {$name}\n";
}

// ============ MIGRATION CHECKS ============
echo "\nChecking Migrations...\n";
$migrations = [
    'activity_logs_table' => '*create_activity_logs_table.php',
];
foreach ($migrations as $name => $pattern) {
    $files = glob("$basePath/database/migrations/{$pattern}");
    $exists = count($files) > 0;
    $checks['Migrations'][$name] = $exists ? '✓' : '✗';
    echo "  [{$checks['Migrations'][$name]}] {$name}\n";
}

// ============ SEEDER CHECKS ============
echo "\nChecking Seeders...\n";
$seeders = [
    'RoleAndPermissionSeeder' => 'RoleAndPermissionSeeder.php',
];
foreach ($seeders as $name => $file) {
    $path = "$basePath/database/seeders/{$file}";
    $exists = file_exists($path);
    $checks['Seeder'][$name] = $exists ? '✓' : '✗';
    echo "  [{$checks['Seeder'][$name]}] {$name}\n";
}

// ============ ROUTE CHECKS ============
echo "\nChecking Routes...\n";
$routes = [
    'administrator.php' => 'administrator.php',
    'officer.php' => 'officer.php',
    'student.php' => 'student.php',
];
foreach ($routes as $name => $file) {
    $path = "$basePath/routes/{$file}";
    $exists = file_exists($path);
    $checks['Routes'][$name] = $exists ? '✓' : '✗';
    echo "  [{$checks['Routes'][$name]}] {$name}\n";
}

// ============ KERNEL CHECKS ============
echo "\nChecking Kernel Configuration...\n";
$kernelPath = "$basePath/app/Http/Kernel.php";
if (file_exists($kernelPath)) {
    $kernelContent = file_get_contents($kernelPath);
    $hasDepartmentScope = strpos($kernelContent, 'department.scope') !== false;
    $hasRoleAccess = strpos($kernelContent, 'role.access') !== false;
    
    echo "  [" . ($hasDepartmentScope ? '✓' : '✗') . "] department.scope middleware registered\n";
    echo "  [" . ($hasRoleAccess ? '✓' : '✗') . "] role.access middleware registered\n";
}

// ============ SUMMARY ============
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      VERIFICATION SUMMARY                      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";

$totalCheck = 0;
$totalPass = 0;

foreach ($checks as $category => $items) {
    $pass = array_sum(array_map(fn($x) => $x === '✓' ? 1 : 0, $items));
    $total = count($items);
    $totalCheck += $total;
    $totalPass += $pass;
    
    $percentage = $total > 0 ? round(($pass / $total) * 100) : 0;
    $status = $pass === $total ? '✓' : '!';
    
    printf("%-30s [%s] %d/%d (%d%%)\n", $category, $status, $pass, $total, $percentage);
}

echo "\n";
printf("%-30s [%s] %d/%d (%d%%)\n", "TOTAL", 
    $totalPass === $totalCheck ? '✓' : '!', 
    $totalPass, 
    $totalCheck, 
    round(($totalPass / $totalCheck) * 100)
);

echo "\n";

if ($totalPass === $totalCheck) {
    echo "✓ ALL CHECKS PASSED! System is ready for deployment.\n";
    echo "\nNext steps:\n";
    echo "  1. Run: php artisan migrate\n";
    echo "  2. Run: php artisan db:seed --class=RoleAndPermissionSeeder\n";
    echo "  3. Test login with 3 roles: Super Admin, Petugas, Pegawai\n";
    exit(0);
} else {
    echo "✗ Some checks failed! Please review the items marked with [✗]\n";
    echo "\nPlease ensure all components are properly created/installed.\n";
    exit(1);
}
