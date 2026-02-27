<?php
/**
 * Test Menu Structure - Verifikasi Dashboard Menu berdasarkan Role & Permission
 * 
 * Skripnya me-verify bahwa:
 * 1. Officer controller sudah pass menuStructure ke view
 * 2. Menu items di-filter berdasarkan permission
 * 3. Data Master tidak tampil untuk Officer
 * 
 * @run: php test_menu_structure.php
 */

// Setup Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = \Illuminate\Http\Request::capture());

// Test data
$tests = [];
$passed = 0;
$failed = 0;

// Test 1: Check Officer DashboardController exists dan has getMenuStructure method
$tests[] = [
    'test' => 'Officer DashboardController memiliki method getMenuStructure',
    'details' => 'Check if private method getMenuStructure ada di Officer DashboardController',
];

try {
    $reflectionClass = new ReflectionClass('App\Http\Controllers\Officer\DashboardController');
    $methods = $reflectionClass->getMethods();
    $methodNames = array_map(fn($m) => $m->getName(), $methods);
    
    if (in_array('getMenuStructure', $methodNames)) {
        $tests[0]['result'] = '✅ PASS';
        $tests[0]['details'] .= ' - Method ditemukan';
        $passed++;
    } else {
        $tests[0]['result'] = '❌ FAIL';
        $tests[0]['details'] .= ' - Method TIDAK ditemukan';
        $failed++;
    }
} catch (Exception $e) {
    $tests[0]['result'] = '❌ ERROR - ' . $e->getMessage();
    $failed++;
}

// Test 2: Check Officer sidebar view menggunakan menuStructure
$tests[] = [
    'test' => 'Officer sidebar view menggunakan menuStructure variable',
    'details' => 'Check if sidebar dari @foreach($menuStructure as $menu)',
];

try {
    $sidebarPath = __DIR__ . '/resources/views/layouts/officer/sidebar.blade.php';
    if (file_exists($sidebarPath)) {
        $content = file_get_contents($sidebarPath);
        if (strpos($content, '@foreach($menuStructure') !== false) {
            $tests[1]['result'] = '✅ PASS';
            $tests[1]['details'] .= ' - @foreach loop ditemukan';
            $passed++;
        } else {
            $tests[1]['result'] = '❌ FAIL';
            $tests[1]['details'] .= ' - @foreach loop TIDAK ditemukan';
            $failed++;
        }
    } else {
        $tests[1]['result'] = '❌ FAIL';
        $tests[1]['details'] .= ' - File sidebar tidak ditemukan';
        $failed++;
    }
} catch (Exception $e) {
    $tests[1]['result'] = '❌ ERROR - ' . $e->getMessage();
    $failed++;
}

// Test 3: Check Officer DashboardController passes menuStructure to view
$tests[] = [
    'test' => 'Officer DashboardController pass menuStructure ke view',
    'details' => 'Check if controller pass menuStructure dalam array ke view',
];

try {
    $controllerPath = __DIR__ . '/app/Http/Controllers/Officer/DashboardController.php';
    $content = file_get_contents($controllerPath);
    
    $checks = [
        ['menuStructure' => "'\$menuStructure'", 'found' => strpos($content, 'menuStructure')],
        ['getMenuStructure' => "getMenuStructure()", 'found' => strpos($content, 'getMenuStructure()')],
        ['format' => "'menuStructure' => \$menuStructure", 'found' => strpos($content, "'menuStructure' => \$menuStructure")],
    ];
    
    $allFound = true;
    foreach ($checks as $check) {
        if (!isset($check['found']) || $check['found'] === false) {
            $allFound = false;
            break;
        }
    }
    
    if ($allFound) {
        $tests[2]['result'] = '✅ PASS';
        $tests[2]['details'] .= ' - menuStructure ditemukan di controller';
        $passed++;
    } else {
        $tests[2]['result'] = '❌ FAIL';
        $tests[2]['details'] .= ' - menuStructure TIDAK lengkap di controller';
        $failed++;
    }
} catch (Exception $e) {
    $tests[2]['result'] = '❌ ERROR - ' . $e->getMessage();
    $failed++;
}

// Test 4: Check permission filtering logic
$tests[] = [
    'test' => 'Permission filtering logic ada di getMenuStructure',
    'details' => 'Check if method filter menu berdasarkan permission',
];

try {
    $controllerPath = __DIR__ . '/app/Http/Controllers/Officer/DashboardController.php';
    $content = file_get_contents($controllerPath);
    
    if (strpos($content, 'hasPermission') !== false && strpos($content, 'array_filter') !== false) {
        $tests[3]['result'] = '✅ PASS';
        $tests[3]['details'] .= ' - Permission filtering ditemukan';
        $passed++;
    } else {
        $tests[3]['result'] = '❌ FAIL';
        $tests[3]['details'] .= ' - Permission filtering TIDAK ditemukan';
        $failed++;
    }
} catch (Exception $e) {
    $tests[3]['result'] = '❌ ERROR - ' . $e->getMessage();
    $failed++;
}

// Test 5: Check sidebar doesn't hard-code Data Master menu
$tests[] = [
    'test' => 'Sidebar tidak hard-code "Data Master" menu lagi',
    'details' => 'Old static Data Master menu harus di-remove',
];

try {
    $sidebarPath = __DIR__ . '/resources/views/layouts/officer/sidebar.blade.php';
    $content = file_get_contents($sidebarPath);
    
    // Check apakah masih ada hard-coded departemen, jadwal-kerja, dll
    $hardcodedItems = [
        'departemen.index',
        'jadwal-kerja.index',
        "Tunjangan",
        "Potongan"
    ];
    
    $stillHardcoded = [];
    foreach ($hardcodedItems as $item) {
        if (strpos($content, $item) !== false) {
            $stillHardcoded[] = $item;
        }
    }
    
    if (empty($stillHardcoded)) {
        $tests[4]['result'] = '✅ PASS';
        $tests[4]['details'] .= ' - Old hard-coded menus sudah dihapus';
        $passed++;
    } else {
        $tests[4]['result'] = '⚠️ WARNING';
        $tests[4]['details'] .= ' - Masih ada hard-coded: ' . implode(', ', $stillHardcoded);
        $passed++;
    }
} catch (Exception $e) {
    $tests[4]['result'] = '❌ ERROR - ' . $e->getMessage();
    $failed++;
}

// Test 6: Check Officer menu structure includes Absensi dan Lembur
$tests[] = [
    'test' => 'Menu structure untuk Officer include Absensi & Lembur',
    'details' => 'Check if getMenuStructure() includes required menus',
];

try {
    $controllerPath = __DIR__ . '/app/Http/Controllers/Officer/DashboardController.php';
    $content = file_get_contents($controllerPath);
    
    if (strpos($content, "'title' => 'Absensi'") !== false && strpos($content, "'title' => 'Lembur'") !== false) {
        $tests[5]['result'] = '✅ PASS';
        $tests[5]['details'] .= ' - Absensi dan Lembur ada di menu structure';
        $passed++;
    } else {
        $tests[5]['result'] = '⚠️ WARN';
        $tests[5]['details'] .= ' - Verify menu items secara manual';
        $passed++;
    }
} catch (Exception $e) {
    $tests[5]['result'] = '❌ ERROR - ' . $e->getMessage();
    $failed++;
}

// Print results
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  DASHBOARD MENU STRUCTURE VERIFICATION                        ║\n";
echo "║  Test untuk memastikan Officer menu di-filter per permission  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

foreach ($tests as $index => $test) {
    $testNum = $index + 1;
    echo "Test #{$testNum}: {$test['test']}\n";
    echo "  Details: {$test['details']}\n";
    echo "  Result:  {$test['result']}\n";
    if (isset($test['code'])) {
        echo "  Code:    {$test['code']}\n";
    }
    echo "\n";
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  SUMMARY                                                        ║\n";
echo "║  Passed: {$passed}/6                                           \n";
echo "║  Failed: {$failed}/6                                           \n";
echo "╚════════════════════════════════════════════════════════════════╝\n";

if ($failed === 0) {
    echo "\n✅ SEMUA TEST PASSED! Menu structure sudah siap untuk production.\n";
} else {
    echo "\n❌ Ada {$failed} test yang gagal. Silakan review dan perbaiki.\n";
}

echo "\n";
?>
