# IMPLEMENTASI TEKNIS - PRODUCTION-READY PAYROLL SYSTEM

## FILE-FILE YANG PERLU DIUPDATE / DIBUAT

---

## 1. UPDATED PERMISSION SEEDER

### File: `database/seeders/RoleAndPermissionSeeder.php`

Status: **NEEDS UPDATE** - Comprehensive permission structure untuk 3 role

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('role_permission')->truncate();
        Permission::truncate();
        Role::truncate();

        // ====================================
        // CREATE PERMISSIONS (Comprehensive)
        // ====================================

        $permissions = [
            // ========== DASHBOARD ==========
            ['nama_permission' => 'dashboard.view', 'deskripsi' => 'View dashboard', 'kategori' => 'dashboard'],

            // ========== USER & ROLE MANAGEMENT ==========
            ['nama_permission' => 'user.view', 'deskripsi' => 'View all users', 'kategori' => 'user_management'],
            ['nama_permission' => 'user.create', 'deskripsi' => 'Create new user', 'kategori' => 'user_management'],
            ['nama_permission' => 'user.edit', 'deskripsi' => 'Edit user', 'kategori' => 'user_management'],
            ['nama_permission' => 'user.delete', 'deskripsi' => 'Delete user', 'kategori' => 'user_management'],
            ['nama_permission' => 'user.assign_role', 'deskripsi' => 'Assign role to user', 'kategori' => 'user_management'],
            ['nama_permission' => 'user.reset_password', 'deskripsi' => 'Reset user password', 'kategori' => 'user_management'],

            ['nama_permission' => 'role.view', 'deskripsi' => 'View all roles', 'kategori' => 'user_management'],
            ['nama_permission' => 'role.create', 'deskripsi' => 'Create new role', 'kategori' => 'user_management'],
            ['nama_permission' => 'role.edit', 'deskripsi' => 'Edit role', 'kategori' => 'user_management'],
            ['nama_permission' => 'role.delete', 'deskripsi' => 'Delete role', 'kategori' => 'user_management'],

            ['nama_permission' => 'permission.view', 'deskripsi' => 'View permissions', 'kategori' => 'user_management'],
            ['nama_permission' => 'permission.manage', 'deskripsi' => 'Manage permissions', 'kategori' => 'user_management'],

            // ========== PEGAWAI MANAGEMENT ==========
            ['nama_permission' => 'pegawai.view', 'deskripsi' => 'View all pegawai', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.view_own', 'deskripsi' => 'View own profile', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.create', 'deskripsi' => 'Create pegawai', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.edit', 'deskripsi' => 'Edit pegawai', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.delete', 'deskripsi' => 'Delete pegawai', 'kategori' => 'pegawai'],
            ['nama_permission' => 'pegawai.edit_own', 'deskripsi' => 'Edit own profile (limited)', 'kategori' => 'pegawai'],

            // ========== DEPARTEMEN ==========
            ['nama_permission' => 'departemen.view', 'deskripsi' => 'View departemen', 'kategori' => 'departemen'],
            ['nama_permission' => 'departemen.create', 'deskripsi' => 'Create departemen', 'kategori' => 'departemen'],
            ['nama_permission' => 'departemen.edit', 'deskripsi' => 'Edit departemen', 'kategori' => 'departemen'],
            ['nama_permission' => 'departemen.delete', 'deskripsi' => 'Delete departemen', 'kategori' => 'departemen'],

            // ========== JABATAN ==========
            ['nama_permission' => 'jabatan.view', 'deskripsi' => 'View jabatan', 'kategori' => 'jabatan'],
            ['nama_permission' => 'jabatan.create', 'deskripsi' => 'Create jabatan', 'kategori' => 'jabatan'],
            ['nama_permission' => 'jabatan.edit', 'deskripsi' => 'Edit jabatan', 'kategori' => 'jabatan'],
            ['nama_permission' => 'jabatan.delete', 'deskripsi' => 'Delete jabatan', 'kategori' => 'jabatan'],

            // ========== TUNJANGAN ==========
            ['nama_permission' => 'tunjangan.view', 'deskripsi' => 'View tunjangan', 'kategori' => 'tunjangan'],
            ['nama_permission' => 'tunjangan.create', 'deskripsi' => 'Create tunjangan', 'kategori' => 'tunjangan'],
            ['nama_permission' => 'tunjangan.edit', 'deskripsi' => 'Edit tunjangan', 'kategori' => 'tunjangan'],
            ['nama_permission' => 'tunjangan.delete', 'deskripsi' => 'Delete tunjangan', 'kategori' => 'tunjangan'],
            ['nama_permission' => 'tunjangan.assign', 'deskripsi' => 'Assign tunjangan', 'kategori' => 'tunjangan'],

            // ========== POTONGAN ==========
            ['nama_permission' => 'potongan.view', 'deskripsi' => 'View potongan', 'kategori' => 'potongan'],
            ['nama_permission' => 'potongan.create', 'deskripsi' => 'Create potongan', 'kategori' => 'potongan'],
            ['nama_permission' => 'potongan.edit', 'deskripsi' => 'Edit potongan', 'kategori' => 'potongan'],
            ['nama_permission' => 'potongan.delete', 'deskripsi' => 'Delete potongan', 'kategori' => 'potongan'],
            ['nama_permission' => 'potongan.assign', 'deskripsi' => 'Assign potongan', 'kategori' => 'potongan'],

            // ========== ABSENSI ==========
            ['nama_permission' => 'absensi.view', 'deskripsi' => 'View absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.view_own', 'deskripsi' => 'View own absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.create', 'deskripsi' => 'Create absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.edit', 'deskripsi' => 'Edit absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.delete', 'deskripsi' => 'Delete absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.approve', 'deskripsi' => 'Approve absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.reject', 'deskripsi' => 'Reject absensi', 'kategori' => 'absensi'],
            ['nama_permission' => 'absensi.request_correction', 'deskripsi' => 'Request absensi correction', 'kategori' => 'absensi'],

            // ========== LEMBUR ==========
            ['nama_permission' => 'lembur.view', 'deskripsi' => 'View lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.view_own', 'deskripsi' => 'View own lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.create', 'deskripsi' => 'Create lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.edit', 'deskripsi' => 'Edit lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.delete', 'deskripsi' => 'Delete lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.approve', 'deskripsi' => 'Approve lembur', 'kategori' => 'lembur'],
            ['nama_permission' => 'lembur.reject', 'deskripsi' => 'Reject lembur', 'kategori' => 'lembur'],

            // ========== GAJI / PENGGAJIAN ==========
            ['nama_permission' => 'gaji.view', 'deskripsi' => 'View salary', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.view_own', 'deskripsi' => 'View own salary', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.create', 'deskripsi' => 'Create salary/payroll entry', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.calculate', 'deskripsi' => 'Calculate salary', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.edit', 'deskripsi' => 'Edit salary (draft only)', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.delete', 'deskripsi' => 'Delete salary', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.approve', 'deskripsi' => 'Approve salary', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.post', 'deskripsi' => 'Post salary to payroll', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.print_slip', 'deskripsi' => 'Print payslip', 'kategori' => 'gaji'],
            ['nama_permission' => 'gaji.export', 'deskripsi' => 'Export salary data', 'kategori' => 'gaji'],

            // ========== LAPORAN ==========
            ['nama_permission' => 'laporan.view', 'deskripsi' => 'View reports', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.absensi', 'deskripsi' => 'View attendance reports', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.lembur', 'deskripsi' => 'View overtime reports', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.gaji', 'deskripsi' => 'View salary reports', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.export', 'deskripsi' => 'Export reports', 'kategori' => 'laporan'],
            ['nama_permission' => 'laporan.budget_vs_actual', 'deskripsi' => 'View budget vs actual', 'kategori' => 'laporan'],

            // ========== SYSTEM & MAINTENANCE ==========
            ['nama_permission' => 'system.config', 'deskripsi' => 'System configuration', 'kategori' => 'system'],
            ['nama_permission' => 'system.backup', 'deskripsi' => 'Backup management', 'kategori' => 'system'],
            ['nama_permission' => 'system.activity_log', 'deskripsi' => 'View activity logs', 'kategori' => 'system'],
            ['nama_permission' => 'system.email_config', 'deskripsi' => 'Email configuration', 'kategori' => 'system'],
            ['nama_permission' => 'system.integration', 'deskripsi' => 'External integrations', 'kategori' => 'system'],

            // ========== PROFILE ==========
            ['nama_permission' => 'profile.view', 'deskripsi' => 'View profile', 'kategori' => 'profile'],
            ['nama_permission' => 'profile.edit', 'deskripsi' => 'Edit profile', 'kategori' => 'profile'],
            ['nama_permission' => 'profile.change_password', 'deskripsi' => 'Change password', 'kategori' => 'profile'],
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // ====================================
        // CREATE 3 MAIN ROLES
        // ====================================

        // 1. SUPER ADMIN - Full Access
        $superAdmin = Role::create([
            'nama_role' => 'Super Admin',
            'deskripsi_role' => 'Administrator sistem penuh dengan akses ke semua fitur'
        ]);

        // Assign ALL permissions to Super Admin
        $allPermissions = Permission::pluck('id_permission')->toArray();
        $superAdmin->permissions()->attach($allPermissions);

        // 2. PETUGAS (OFFICER) - Department-based Limited Access
        $officer = Role::create([
            'nama_role' => 'Petugas',
            'deskripsi_role' => 'Petugas departemen - kelola data sesuai departemen sendiri'
        ]);

        // Assign Officer permissions
        $officerPermissions = [
            'dashboard.view',
            'pegawai.view',
            'departemen.view',
            'jabatan.view',
            'tunjangan.view',
            'potongan.view',
            'absensi.view',
            'absensi.create',
            'absensi.edit',
            'absensi.delete',
            'absensi.approve',
            'absensi.reject',
            'lembur.view',
            'lembur.create',
            'lembur.edit',
            'lembur.delete',
            'lembur.approve',
            'lembur.reject',
            'gaji.view',
            'gaji.print_slip',
            'gaji.export',
            'laporan.view',
            'laporan.absensi',
            'laporan.lembur',
            'laporan.gaji',
            'laporan.export',
            'laporan.budget_vs_actual',
            'profile.view',
            'profile.edit',
            'profile.change_password',
        ];

        $officerPerms = Permission::whereIn('nama_permission', $officerPermissions)->pluck('id_permission')->toArray();
        $officer->permissions()->attach($officerPerms);

        // 3. PEGAWAI (EMPLOYEE) - Self-Service Only
        $employee = Role::create([
            'nama_role' => 'Pegawai',
            'deskripsi_role' => 'Pegawai - lihat data pribadi saja'
        ]);

        // Assign Employee permissions
        $employeePermissions = [
            'dashboard.view',
            'pegawai.view_own',
            'absensi.view_own',
            'absensi.request_correction',
            'lembur.view_own',
            'gaji.view_own',
            'gaji.print_slip',
            'profile.view',
            'profile.edit',
            'profile.change_password',
        ];

        $employeePerms = Permission::whereIn('nama_permission', $employeePermissions)->pluck('id_permission')->toArray();
        $employee->permissions()->attach($employeePerms);

        $this->command->info('✅ Role dan Permission berhasil dibuat!');
        $this->command->line('  - Super Admin: ' . count($allPermissions) . ' permissions');
        $this->command->line('  - Petugas: ' . count($officerPerms) . ' permissions');
        $this->command->line('  - Pegawai: ' . count($employeePerms) . ' permissions');
    }
}
```

---

## 2. CUSTOM MIDDLEWARE

### File: `app/Http/Middleware/DepartmentScope.php`

Status: **NEW** - Middleware untuk department-based filtering

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DepartmentScope
{
    /**
     * Middleware untuk memastikan Officer hanya akses data departemennya
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika user adalah Officer (guard: officer)
        if (auth('officer')->check()) {
            $officer = auth('officer')->user();
            $departmentId = $officer->id_departemen;
            
            // Simpan di request untuk digunakan di controller/query
            $request->merge(['filtered_department_id' => $departmentId]);
            
            // Validasi akses jika ada department_id di URL
            if ($request->has('department_id')) {
                if ($request->department_id != $departmentId) {
                    abort(403, 'Anda tidak memiliki akses ke departemen ini');
                }
            }
        }
        
        return $next($request);
    }
}
```

### File: `app/Http/Middleware/DataVisibility.php`

Status: **NEW** - Middleware untuk data visibility filter

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DataVisibility
{
    /**
     * Middleware untuk filter data visibility berdasarkan role
     * 
     * - Super Admin: lihat semua
     * - Officer: lihat data departemen sendiri
     * - Employee: lihat data pribadi saja
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }

        // For Super Admin - No filtering needed
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // For Officer - Filter by department
        if ($user->hasRole('Petugas')) {
            $officer = auth('officer')->user();
            $request->attributes->add(['user_department_id' => $officer->id_departemen]);
        }

        // For Employee - Filter by self only
        if ($user->hasRole('Pegawai')) {
            $employee = auth('student')->user();
            $request->attributes->add(['user_pegawai_id' => $employee->id_pegawai]);
        }

        return $next($request);
    }
}
```

---

## 3. REGISTER MIDDLEWARE

### File: `app/Http/Kernel.php`

Update `routeMiddleware`:

```php
protected $routeMiddleware = [
    // ... existing middleware ...
    'department.scope' => \App\Http\Middleware\DepartmentScope::class,
    'data.visibility' => \App\Http\Middleware\DataVisibility::class,
];
```

---

## 4. UPDATE ROUTES

### File: `routes/administrator.php`

```php
<?php

use App\Http\Controllers\Administrator\DashboardController;
use App\Http\Controllers\Administrator\PenggajianController;
use App\Http\Controllers\Administrator\AbsensiController;
use App\Http\Controllers\Administrator\LemburController;
use App\Http\Controllers\Administrator\TunjanganController;
use App\Http\Controllers\Administrator\PotonganController;
use App\Http\Controllers\Administrator\PegawaiController;
use App\Http\Controllers\Administrator\DepartemenController;
use App\Http\Controllers\Administrator\JabatanController;
use App\Http\Controllers\Administrator\UserController;
use App\Http\Controllers\Administrator\RoleController;
use App\Http\Controllers\Administrator\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:administrator', 'permission:dashboard.view'])->name('administrators.')->prefix('administrator')->group(function () {
    
    // ========== DASHBOARD ==========
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ========== USER & ROLE MANAGEMENT ==========
    Route::middleware('permission:user.view')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });

    Route::middleware('permission:role.view')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    Route::middleware('permission:permission.view')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // ========== MASTER DATA ==========
    Route::middleware('permission:pegawai.view')->group(function () {
        Route::resource('pegawai', PegawaiController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:departemen.view')->group(function () {
        Route::resource('departemen', DepartemenController::class);
    });

    Route::middleware('permission:jabatan.view')->group(function () {
        Route::resource('jabatan', JabatanController::class);
    });

    // ========== KOMPONEN GAJI ==========
    Route::middleware('permission:tunjangan.view')->group(function () {
        Route::resource('tunjangan', TunjanganController::class);
    });

    Route::middleware('permission:potongan.view')->group(function () {
        Route::resource('potongan', PotonganController::class);
    });

    // ========== ABSENSI ==========
    Route::middleware('permission:absensi.view')->group(function () {
        Route::resource('absensi', AbsensiController::class)->only(['index', 'show']);
        Route::middleware('permission:absensi.create')->post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::middleware('permission:absensi.edit')->put('/absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
        Route::middleware('permission:absensi.delete')->delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
        Route::middleware('permission:absensi.approve')->post('/absensi/{id}/approve', [AbsensiController::class, 'approve'])->name('absensi.approve');
    });

    // ========== LEMBUR ==========
    Route::middleware('permission:lembur.view')->group(function () {
        Route::resource('lembur', LemburController::class)->only(['index', 'show']);
        Route::middleware('permission:lembur.create')->post('/lembur', [LemburController::class, 'store'])->name('lembur.store');
        Route::middleware('permission:lembur.edit')->put('/lembur/{id}', [LemburController::class, 'update'])->name('lembur.update');
        Route::middleware('permission:lembur.delete')->delete('/lembur/{id}', [LemburController::class, 'destroy'])->name('lembur.destroy');
        Route::middleware('permission:lembur.approve')->post('/lembur/{id}/approve', [LemburController::class, 'approve'])->name('lembur.approve');
    });

    // ========== PENGGAJIAN ==========
    Route::middleware('permission:gaji.view')->group(function () {
        Route::get('/penggajian', [PenggajianController::class, 'index'])->name('penggajian.index');
        Route::get('/penggajian/{id}', [PenggajianController::class, 'show'])->name('penggajian.show');
        
        Route::middleware('permission:gaji.calculate')->post('/penggajian/calculate', [PenggajianController::class, 'calculate'])->name('penggajian.calculate');
        Route::middleware('permission:gaji.approve')->post('/penggajian/{id}/approve', [PenggajianController::class, 'approve'])->name('penggajian.approve');
        Route::middleware('permission:gaji.post')->post('/penggajian/{id}/post', [PenggajianController::class, 'post'])->name('penggajian.post');
        Route::middleware('permission:gaji.print_slip')->get('/penggajian/{id}/slip', [PenggajianController::class, 'printSlip'])->name('penggajian.print_slip');
        Route::middleware('permission:gaji.export')->get('/penggajian/export', [PenggajianController::class, 'export'])->name('penggajian.export');
    });

});
```

### File: `routes/officer.php`

```php
<?php

use App\Http\Controllers\Officer\DashboardController;
use App\Http\Controllers\Officer\PegawaiController;
use App\Http\Controllers\Officer\AbsensiController;
use App\Http\Controllers\Officer\LemburController;
use App\Http\Controllers\Officer\PenggajianController;
use App\Http\Controllers\Officer\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:officer', 'permission:dashboard.view', 'department.scope'])->name('officers.')->prefix('officer')->group(function () {
    
    // ========== DASHBOARD ==========
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ========== MY TEAM ==========
    Route::middleware('permission:pegawai.view')->group(function () {
        Route::get('pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('pegawai/{id}', [PegawaiController::class, 'show'])->name('pegawai.show');
    });

    // ========== ABSENSI (Own Department Only) ==========
    Route::middleware('permission:absensi.view')->group(function () {
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/{id}', [AbsensiController::class, 'show'])->name('absensi.show');
        
        Route::middleware('permission:absensi.create')->post('absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::middleware('permission:absensi.edit')->put('absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
        Route::middleware('permission:absensi.delete')->delete('absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
        Route::middleware('permission:absensi.approve')->post('absensi/{id}/approve', [AbsensiController::class, 'approve'])->name('absensi.approve');
        Route::middleware('permission:absensi.reject')->post('absensi/{id}/reject', [AbsensiController::class, 'reject'])->name('absensi.reject');
    });

    // ========== LEMBUR (Own Department Only) ==========
    Route::middleware('permission:lembur.view')->group(function () {
        Route::get('lembur', [LemburController::class, 'index'])->name('lembur.index');
        Route::get('lembur/{id}', [LemburController::class, 'show'])->name('lembur.show');
        
        Route::middleware('permission:lembur.create')->post('lembur', [LemburController::class, 'store'])->name('lembur.store');
        Route::middleware('permission:lembur.edit')->put('lembur/{id}', [LemburController::class, 'update'])->name('lembur.update');
        Route::middleware('permission:lembur.delete')->delete('lembur/{id}', [LemburController::class, 'destroy'])->name('lembur.destroy');
        Route::middleware('permission:lembur.approve')->post('lembur/{id}/approve', [LemburController::class, 'approve'])->name('lembur.approve');
        Route::middleware('permission:lembur.reject')->post('lembur/{id}/reject', [LemburController::class, 'reject'])->name('lembur.reject');
    });

    // ========== PENGGAJIAN (View Only) ==========
    Route::middleware('permission:gaji.view')->group(function () {
        Route::get('penggajian', [PenggajianController::class, 'index'])->name('penggajian.index');
        Route::get('penggajian/{id}', [PenggajianController::class, 'show'])->name('penggajian.show');
        Route::middleware('permission:gaji.print_slip')->get('penggajian/{id}/slip', [PenggajianController::class, 'printSlip'])->name('penggajian.slip');
        Route::middleware('permission:gaji.export')->get('penggajian/export', [PenggajianController::class, 'export'])->name('penggajian.export');
    });

    // ========== REPORTS (Own Department Only) ==========
    Route::middleware('permission:laporan.view')->group(function () {
        Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/overtime', [ReportController::class, 'overtime'])->name('reports.overtime');
        Route::get('/reports/salary', [ReportController::class, 'salary'])->name('reports.salary');
    });

});
```

### File: `routes/student.php`

```php
<?php

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\AbsensiController;
use App\Http\Controllers\Student\LemburController;
use App\Http\Controllers\Student\PenggajianController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:student', 'permission:dashboard.view'])->name('students.')->prefix('student')->group(function () {
    
    // ========== DASHBOARD ==========
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ========== MY PROFILE ==========
    Route::middleware('permission:profile.view')->group(function () {
        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::middleware('permission:profile.edit')->put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::middleware('permission:profile.change_password')->post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change_password');
    });

    // ========== MY ATTENDANCE ==========
    Route::middleware('permission:absensi.view_own')->group(function () {
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/{id}', [AbsensiController::class, 'show'])->name('absensi.show');
        Route::middleware('permission:absensi.request_correction')->post('absensi/{id}/request-correction', [AbsensiController::class, 'requestCorrection'])->name('absensi.request_correction');
    });

    // ========== MY OVERTIME ==========
    Route::middleware('permission:lembur.view_own')->group(function () {
        Route::get('lembur', [LemburController::class, 'index'])->name('lembur.index');
        Route::get('lembur/{id}', [LemburController::class, 'show'])->name('lembur.show');
    });

    // ========== MY SALARY ==========
    Route::middleware('permission:gaji.view_own')->group(function () {
        Route::get('penggajian', [PenggajianController::class, 'index'])->name('penggajian.index');
        Route::get('penggajian/{id}', [PenggajianController::class, 'show'])->name('penggajian.show');
        Route::middleware('permission:gaji.print_slip')->get('penggajian/{id}/slip', [PenggajianController::class, 'printSlip'])->name('penggajian.slip');
    });

});
```

---

## 5. CONTROLLER HELPER - DATA FILTERING

### File: `app/Http/Controllers/BaseController.php`

Status: **NEW** - Base controller dengan helper untuk filtering

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Get department ID untuk filtering Officer data
     */
    protected function getDepartmentIdFilter()
    {
        if (auth('officer')->check()) {
            return auth('officer')->user()->id_departemen;
        }
        return null;
    }

    /**
     * Get pegawai ID untuk filtering Employee data
     */
    protected function getPegawaiIdFilter()
    {
        if (auth('student')->check()) {
            return auth('student')->user()->id_pegawai;
        }
        return null;
    }

    /**
     * Get user role untuk menentukan access level
     */
    protected function getUserRole()
    {
        $user = auth()->user();
        return $user->role->nama_role ?? null;
    }

    /**
     * Check if user is Super Admin
     */
    protected function isSuperAdmin()
    {
        return $this->getUserRole() === 'Super Admin';
    }

    /**
     * Check if user is Officer
     */
    protected function isOfficer()
    {
        return auth('officer')->check();
    }

    /**
     * Check if user is Employee
     */
    protected function isEmployee()
    {
        return auth('student')->check();
    }

    /**
     * Build query scope dengan filtering
     * Usage: $query = $this->applyDataScope(Absensi::query());
     */
    protected function applyDataScope($query)
    {
        if ($this->isSuperAdmin()) {
            // Super Admin: No filter
            return $query;
        }

        if ($this->isOfficer()) {
            // Officer: Filter by department
            $deptId = $this->getDepartmentIdFilter();
            return $query->whereHas('pegawai', function ($q) use ($deptId) {
                $q->where('id_departemen', $deptId);
            });
        }

        if ($this->isEmployee()) {
            // Employee: Filter by self only
            $pegawaiId = $this->getPegawaiIdFilter();
            return $query->where('id_pegawai', $pegawaiId);
        }

        return $query;
    }

    /**
     * Success response
     */
    protected function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error response
     */
    protected function error($message = 'Error', $data = null, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
```

---

## 6. EXAMPLE: ABSENSI CONTROLLER

### File: `app/Http/Controllers/Officer/AbsensiController.php`

Status: **EXAMPLE** - Implementation pattern untuk Officer

```php
<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\BaseController;
use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class AbsensiController extends BaseController
{
    /**
     * Display list of absensi (Own Department Only)
     */
    public function index(Request $request)
    {
        $deptId = $this->getDepartmentIdFilter();

        $absensi = Absensi::query()
            ->whereHas('pegawai', function ($q) use ($deptId) {
                $q->where('id_departemen', $deptId);
            })
            ->with('pegawai')
            ->when($request->month, function ($q) use ($request) {
                $q->whereMonth('tanggal_absensi', $request->month);
            })
            ->when($request->year, function ($q) use ($request) {
                $q->whereYear('tanggal_absensi', $request->year);
            })
            ->when($request->pegawai_id, function ($q) use ($request) {
                $q->where('id_pegawai', $request->pegawai_id);
            })
            ->paginate(50);

        return view('officer.absensi.index', compact('absensi'));
    }

    /**
     * Show detail absensi
     */
    public function show($id)
    {
        $absensi = Absensi::findOrFail($id);
        $deptId = $this->getDepartmentIdFilter();

        // Verify ownership (department filtering)
        if ($absensi->pegawai->id_departemen != $deptId) {
            abort(403, 'Unauthorized access');
        }

        return view('officer.absensi.show', compact('absensi'));
    }

    /**
     * Store absensi (Own Department Only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'tanggal_absensi' => 'required|date',
            'status_absensi' => 'required|in:H,S,I,L,C,A',
            'catatan' => 'nullable|string',
        ]);

        // Verify: Pegawai milik department ini
        $pegawai = Pegawai::findOrFail($validated['id_pegawai']);
        if ($pegawai->id_departemen != $this->getDepartmentIdFilter()) {
            abort(403, 'Pegawai tidak ada di departemen Anda');
        }

        // Create absensi
        $absensi = Absensi::create([
            ...$validated,
            'status' => 'DRAFT',
            'created_by' => auth('officer')->id(),
        ]);

        return redirect()->route('officers.absensi.show', $absensi->id)
            ->with('success', 'Absensi berhasil dibuat');
    }

    /**
     * Approve absensi (Officer dapat approve data timnya)
     */
    public function approve(Request $request, $id)
    {
        $absensi = Absensi::findOrFail($id);
        $deptId = $this->getDepartmentIdFilter();

        // Verify ownership
        if ($absensi->pegawai->id_departemen != $deptId) {
            abort(403, 'Unauthorized');
        }

        // Check permission
        if (!auth('officer')->user()->hasPermission('absensi.approve')) {
            abort(403, 'Tidak memiliki izin approve absensi');
        }

        // Update status
        $absensi->update([
            'status' => 'APPROVED',
            'approved_by' => auth('officer')->id(),
            'approved_at' => now(),
        ]);

        // Log activity
        activity()
            ->performedOn($absensi)
            ->withProperties(['action' => 'approved'])
            ->log('Absensi approved by Officer');

        return redirect()->back()
            ->with('success', 'Absensi berhasil di-approve');
    }

    /**
     * Reject absensi
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'alasan_reject' => 'required|string',
        ]);

        $absensi = Absensi::findOrFail($id);
        $deptId = $this->getDepartmentIdFilter();

        if ($absensi->pegawai->id_departemen != $deptId) {
            abort(403, 'Unauthorized');
        }

        $absensi->update([
            'status' => 'REJECTED',
            'alasan_reject' => $validated['alasan_reject'],
            ...$validated,
        ]);

        activity()
            ->performedOn($absensi)
            ->withProperties(['action' => 'rejected', 'reason' => $validated['alasan_reject']])
            ->log('Absensi rejected by Officer');

        return redirect()->back()
            ->with('success', 'Absensi berhasil di-reject');
    }
}
```

---

## 7. ACTIVITY LOG

### File: `config/activity_log_config.php`

Status: **NEEDS SETUP** - Untuk audit trail

```php
return [
    'enabled' => true,
    'database_connection' => env('ACTIVITY_LOG_DATABASE_CONNECTION'),
    'table_name' => 'activity_log',
    'migrations_path' => database_path('migrations'),
    'model' => Spatie\ActivityLog\Models\Activity::class,

    'activity_description' => [
        'created' => ':causer_name membuat :subject_type',
        'updated' => ':causer_name mengubah :subject_type',
        'deleted' => ':causer_name menghapus :subject_type',
        'approved' => ':causer_name approve :subject_type',
        'postings' => ':causer_name posting :subject_type',
    ],

    'logging_models' => [
        'Absensi' => ['created', 'updated', 'deleted', 'approved'],
        'Lembur' => ['created', 'updated', 'deleted', 'approved'],
        'Penggajian' => ['created', 'updated', 'deleted', 'approved', 'posing'],
    ],
];
```

---

## 8. CHECKLIST IMPLEMENTASI TEKNIS

```
☐ Database Update
  ☐ Verify all tables exist with correct structure
  ☐ Add id_role to users, officers, students table
  ☐ Create activity_log table
  ☐ Run migration: RoleAndPermissionSeeder
  ☐ Verify foreign keys

☐ Middleware Setup
  ☐ Create DepartmentScope middleware
  ☐ Create DataVisibility middleware
  ☐ Register in Kernel.php
  ☐ Test middleware functionality

☐ Routes Configuration
  ☐ Update routes/administrator.php with permission checks
  ☐ Update routes/officer.php with department scoping
  ☐ Update routes/student.php with self-filtering
  ☐ Apply middleware to each route
  ☐ Test route accessibility

☐ Controllers
  ☐ Create BaseController with helper methods
  ☐ Update all controllers to use BaseController
  ☐ Implement data filtering in queries
  ☐ Add permission checks before actions
  ☐ Implement audit logging

☐ Views
  ☐ Create permission-based menu items
  ☐ Show/hide sidebar items based on role
  ☐ Show/hide action buttons based on permission
  ☐ Implement responsive design

☐ Testing
  ☐ Test Super Admin access (should have full access)
  ☐ Test Officer access (should only see own department)
  ☐ Test Employee access (should only see own data)
  ☐ Test permission restrictions
  ☐ Test data filtering
  ☐ Test audit logging

☐ Security
  ☐ Implement 2FA for Super Admin
  ☐ Test XSS prevention
  ☐ Test CSRF protection
  ☐ Test SQL injection prevention
  ☐ Test authorization enforcement

☐ Performance
  ☐ Optimize database queries
  ☐ Add caching where needed
  ☐ Load test with multiple users
  ☐ Monitor performance metrics

☐ Documentation
  ☐ Update API documentation
  ☐ Create user manuals per role
  ☐ Document database schema
  ☐ Create troubleshooting guide
```

