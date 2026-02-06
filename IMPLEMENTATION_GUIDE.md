# IMPLEMENTASI SISTEM PENGGAJIAN - QUICK START GUIDE
## PT Digital Solution

---

## 📦 PAKET YANG TELAH DIBUAT

Saya telah membuat sistem perhitungan gaji dan role-permission yang komprehensif untuk PT Digital Solution. Berikut adalah daftar lengkap file dan komponen yang telah dibuat:

### 1. **Service Layer** ✅
- [**SalaryCalculationService.php**](app/Services/SalaryCalculationService.php)
  - Logic perhitungan gaji bulanan yang realistis
  - Menghitung: Absensi, Tunjangan, Lembur, Potongan, Pajak PPh 21
  - Support batch dan single calculation
  - Simpan hasil ke database

### 2. **Model & Database** ✅
- [**Role.php**](app/Models/Role.php) - Model untuk role
- [**Permission.php**](app/Models/Permission.php) - Model untuk permission
- [**HasPermissions.php**](app/Models/HasPermissions.php) - Trait untuk user permission checks
- **User.php** - Update dengan trait dan id_role field

### 3. **Middleware** ✅
- [**CheckPermission.php**](app/Http/Middleware/CheckPermission.php) - Middleware untuk check permission
- [**CheckRole.php**](app/Http/Middleware/CheckRole.php) - Middleware untuk check role

### 4. **Controller** ✅
- [**PenggajianController.php**](app/Http/Controllers/PenggajianController.php)
  - Index (List gaji dengan role-based filtering)
  - Show (Detail gaji dengan security checks)
  - Calculate (Hitung gaji)
  - Store (Simpan perhitungan)
  - Update (Edit perhitungan)
  - Approve (Approval flow)
  - CalculateBatch (Batch processing semua pegawai)
  - PrintSlip (Generate slip gaji)

### 5. **Seeder** ✅
- [**RoleAndPermissionSeeder.php**](database/seeders/RoleAndPermissionSeeder.php)
  - Setup 4 role: Admin HRD, Manager, Direktur, Pegawai
  - Setup 40+ permission
  - Mapping role ke permission

### 6. **Dokumentasi** ✅
- [**SALARY_CALCULATION_DOCUMENTATION.md**](SALARY_CALCULATION_DOCUMENTATION.md)
  - Penjelasan lengkap logic perhitungan gaji
  - Flowchart alur perhitungan
  - Detail setiap komponen gaji
  - Contoh perhitungan end-to-end
  - Panduan implementasi

- [**ROLE_PERMISSION_DOCUMENTATION.md**](ROLE_PERMISSION_DOCUMENTATION.md)
  - Penjelasan role & permission system
  - 4 role dengan akses yang berbeda
  - 40+ permission dengan kategori
  - Implementasi code examples
  - Best practices

- [**ROUTE_SETUP_GAJI.php**](ROUTE_SETUP_GAJI.php)
  - Setup route API untuk gaji
  - Dokumentasi endpoint
  - Contoh request/response

---

## 🚀 QUICK START - LANGKAH IMPLEMENTASI

### STEP 1: Database Migration
```bash
# Jika belum ada tabel role dan permission, buat migration:
php artisan make:migration create_role_permission_tables

# Tambahkan ke migration file:
Schema::create('role', function (Blueprint $table) {
    $table->id('id_role');
    $table->string('nama_role')->unique();
    $table->text('deskripsi')->nullable();
    $table->timestamps();
});

Schema::create('permission', function (Blueprint $table) {
    $table->id('id_permission');
    $table->string('nama_permission')->unique();
    $table->text('deskripsi')->nullable();
    $table->string('kategori')->nullable();
    $table->timestamps();
});

Schema::create('role_permission', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('id_role');
    $table->unsignedBigInteger('id_permission');
    $table->unique(['id_role', 'id_permission']);
    $table->foreign('id_role')->references('id_role')->on('role')->onDelete('cascade');
    $table->foreign('id_permission')->references('id_permission')->on('permission')->onDelete('cascade');
    $table->timestamps();
});

# Update users table
Schema::table('users', function (Blueprint $table) {
    $table->unsignedBigInteger('id_role')->nullable();
    $table->foreign('id_role')->references('id_role')->on('role')->onDelete('set null');
});
```

### STEP 2: Run Migration & Seeder
```bash
# Run migration
php artisan migrate

# Run seeder untuk setup role & permission
php artisan db:seed --class=RoleAndPermissionSeeder
```

### STEP 3: Register Middleware
Edit `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ... middleware lainnya
    'permission' => \App\Http\Middleware\CheckPermission::class,
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

### STEP 4: Setup Routes
Tambahkan ke `routes/api.php` atau `routes/officer.php`:
```php
use App\Http\Controllers\PenggajianController;

Route::middleware('auth:sanctum')->group(function () {
    // List gaji
    Route::get('/gaji', [PenggajianController::class, 'index'])
        ->middleware('permission:gaji.view');
    
    // Hitung gaji (batch)
    Route::post('/gaji/batch', [PenggajianController::class, 'calculateBatch'])
        ->middleware('permission:gaji.create');
    
    // Detail gaji
    Route::get('/gaji/{penggajianId}', [PenggajianController::class, 'show'])
        ->middleware('permission:gaji.view');
    
    // Approve gaji
    Route::post('/gaji/{penggajianId}/approve', [PenggajianController::class, 'approve'])
        ->middleware('permission:gaji.approve');
    
    // Print slip
    Route::get('/gaji/{penggajianId}/print', [PenggajianController::class, 'printSlip'])
        ->middleware('permission:gaji.print_slip');
});
```

### STEP 5: Test Perhitungan Gaji
```php
// Di tinker atau test file
$pegawai = Pegawai::find(1);
$service = app(\App\Services\SalaryCalculationService::class);
$hasil = $service->calculateMonthlySalary($pegawai, '2026-01');

dd($hasil);
```

---

## 💡 CONTOH PENGGUNAAN LENGKAP

### Scenario: Admin HRD Menghitung Gaji Bulanan

```php
// Request dari frontend
POST /api/gaji/batch
Header: Authorization: Bearer {token}
Body: {
  "periode": "2026-01"
}

// Response
{
  "status": "success",
  "message": "Batch calculation selesai",
  "summary": {
    "total": 50,
    "success": 50,
    "failed": 0
  },
  "data": [
    {
      "pegawai_id": 1,
      "nama": "Ahmad Fauzi",
      "gaji_bersih": 16467500
    },
    // ... pegawai lainnya
  ]
}
```

### Scenario: Manager Approve Absensi

```php
// Manager lihat absensi tim
GET /api/gaji?periode=2026-01
Header: Authorization: Bearer {token}

// Server akan filter otomatis untuk departemen manager
// Karena middleware permission sudah check dan controller handle role-based filtering
```

### Scenario: Pegawai Lihat & Print Slip Gaji

```php
// Pegawai lihat slip gajian sendiri
GET /api/gaji/1
Header: Authorization: Bearer {token}
// Request di-authenticate dan di-filter ke gaji pegawai yang login saja

// Print slip
GET /api/gaji/1/print?format=pdf
Header: Authorization: Bearer {token}
// Return PDF slip gaji siap download
```

### Scenario: Direktur Approve Gaji

```php
// Direktur lihat semua perhitungan gaji
GET /api/gaji?periode=2026-01
Header: Authorization: Bearer {token}

// Approve perhitungan
POST /api/gaji/15/approve
Header: Authorization: Bearer {token}

// Response
{
  "status": "success",
  "message": "Perhitungan gaji berhasil di-approve",
  "data": {
    "id_penggajian": 15,
    "status": "approved",
    "approved_at": "2026-01-25 14:30:00",
    "gaji_bersih": 16467500
  }
}
```

---

## 📊 ROLE & PERMISSION MAPPING

| Fitur | Admin HRD | Manager | Direktur | Pegawai |
|-------|:---------:|:-------:|:--------:|:-------:|
| **Gaji** | | | | |
| View all | ✅ | ✅ | ✅ | ❌ |
| View own | ✅ | ✅ | ✅ | ✅ |
| Calculate | ✅ | ❌ | ❌ | ❌ |
| Edit | ✅ | ❌ | ❌ | ❌ |
| Approve | ✅ | ❌ | ✅ | ❌ |
| Print slip | ✅ | ✅ | ✅ | ✅ |
| **Absensi** | | | | |
| View all | ✅ | ✅ | ❌ | ❌ |
| View own | ✅ | ✅ | ❌ | ✅ |
| Input | ✅ | ❌ | ❌ | ❌ |
| Approve | ✅ | ✅ | ❌ | ❌ |
| **Lembur** | | | | |
| View all | ✅ | ✅ | ❌ | ❌ |
| View own | ✅ | ✅ | ❌ | ✅ |
| Input | ✅ | ❌ | ❌ | ❌ |
| Approve | ✅ | ✅ | ❌ | ❌ |

---

## 🔐 SECURITY CHECKLIST

- [x] Setiap route dilindungi `auth:sanctum` middleware
- [x] Setiap endpoint check permission dengan middleware
- [x] Controller tambah additional role-based filtering
- [x] Pegawai hanya bisa lihat data pribadi sendiri (implemented di controller)
- [x] Akses log dicatat untuk audit trail
- [x] Helper method untuk permission checking
- [x] Unauthorized response standard (403, 404)
- [ ] Rate limiting (TODO: Laravel throttle middleware)
- [ ] CORS setup (TODO: Check config/cors.php)
- [ ] Environment variables untuk sensitive data

---

## 🧪 TESTING

### Test End-to-End Perhitungan Gaji

```php
// tests/Feature/SalaryCalculationTest.php
<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Services\SalaryCalculationService;
use Tests\TestCase;

class SalaryCalculationTest extends TestCase
{
    protected $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(SalaryCalculationService::class);
    }

    /** @test */
    public function it_can_calculate_salary()
    {
        $pegawai = Pegawai::find(1);
        
        $result = $this->service->calculateMonthlySalary($pegawai, '2026-01');
        
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('gaji_bersih', $result);
        $this->assertGreaterThan(0, $result['gaji_bersih']);
    }

    /** @test */
    public function it_validates_periode_format()
    {
        $pegawai = Pegawai::find(1);
        
        $result = $this->service->calculateMonthlySalary($pegawai, 'invalid-date');
        
        $this->assertEquals('error', $result['status']);
    }
}
```

### Test Permission Checks

```php
// tests/Feature/SalaryAuthorizationTest.php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Tests\TestCase;

class SalaryAuthorizationTest extends TestCase
{
    /**  @test */
    public function admin_hrd_can_calculate_salary()
    {
        $adminRole = Role::where('nama_role', 'Admin HRD')->first();
        $user = User::factory()->create(['id_role' => $adminRole->id_role]);
        
        $response = $this->actingAs($user)
            ->post('/api/gaji/batch', ['periode' => '2026-01']);
        
        $response->assertStatus(200);
    }

    /** @test */
    public function pegawai_cannot_calculate_salary()
    {
        $pegawaiRole = Role::where('nama_role', 'Pegawai')->first();
        $user = User::factory()->create(['id_role' => $pegawaiRole->id_role]);
        
        $response = $this->actingAs($user)
            ->post('/api/gaji/batch', ['periode' => '2026-01']);
        
        $response->assertStatus(403);
    }
}
```

---

## 📝 DOKUMENTASI FILE

### File Dokumentasi Utama:

1. **SALARY_CALCULATION_DOCUMENTATION.md**
   - Flowchart lengkap proses perhitungan
   - Penjelasan setiap komponen gaji dengan rumus
   - Contoh perhitungan end-to-end
   - Panduan implementasi dengan code

2. **ROLE_PERMISSION_DOCUMENTATION.md**
   - Penjelasan role hierarchy
   - Daftar 40+ permission per kategori
   - Implementasi middleware
   - Best practices & security

3. **ROUTE_SETUP_GAJI.php**
   - Endpoint API lengkap
   - Request/response format
   - Example usage

---

## ❓ FAQ

### Q: Bagaimana cara menambah permission baru?
A: Tambahkan ke tabel `permission` dan assign ke role di `role_permission`:
```php
Permission::create([
    'nama_permission' => 'gaji.reject',
    'deskripsi' => 'Reject/tolak perhitungan gaji',
    'kategori' => 'penggajian'
]);
```

### Q: Bagaimana cara change role user?
A: Update `id_role` di users table:
```php
$user = User::find($userId);
$direktorRole = Role::where('nama_role', 'Direktur')->first();
$user->id_role = $direktorRole->id_role;
$user->save();
```

### Q: Apakah gaji sudah tersimpan otomatis?
A: Tidak. Setelah `calculate()`, harus di-`store()` untuk simpan ke database. Ini untuk memungkinkan review sebelum save.

### Q: Bagaimana dengan koneksi User ke Pegawai?
A: Saat ini User model belum punya relasi eksplisit ke Pegawai. Setup relasi jika diperlukan:
```php
// User.php
public function pegawai()
{
    return $this->hasOne(Pegawai::class, 'id_pegawai', 'id');
}
```

### Q: Apakah sistem mendukung multi-currency?
A: Saat ini hanya Rupiah. Untuk multi-currency, tambahkan field `currency` di Penggajian dan adjust calculation.

---

## 🎯 NEXT STEPS

1. **Database**
   - [ ] Run migration tabel role, permission, role_permission
   - [ ] Run seeder RoleAndPermissionSeeder
   - [ ] Verify data di database

2. **Code Integration**
   - [ ] Copy model Role, Permission, HasPermissions
   - [ ] Copy middleware CheckPermission, CheckRole
   - [ ] Copy controller PenggajianController
   - [ ] Update User model dengan trait & id_role field
   - [ ] Register middleware di Kernel.php
   - [ ] Setup routes di api.php

3. **Testing**
   - [ ] Test permission checks authorization
   - [ ] Test salary calculation logic
   - [ ] Test role-based filtering
   - [ ] Test API endpoints

4. **Frontend Integration**
   - [ ] Update UI untuk show/hide fitur berdasarkan permission
   - [ ] Implement role-based menu
   - [ ] Add permission checks di form submit

5. **Deployment**
   - [ ] Review permissions mapping
   - [ ] Setup admin user dengan role Admin HRD
   - [ ] Test di production
   - [ ] Monitor logs & audit trail

---

## 📞 SUPPORT & TROUBLESHOOTING

### Error: "Call to undefined method hasPermission"
**Solusi**: Pastikan User model sudah use trait `HasPermissions`

### Error: "SQLSTATE[HY000]: General error: 2030 Got error..."
**Solusi**: Migration belum dijalankan. Jalankan `php artisan migrate`

### Error: "No query results for model [Role]"
**Solusi**: Seeder belum dijalankan. Jalankan `php artisan db:seed --class=RoleAndPermissionSeeder`

### Pegawai bisa lihat gaji pegawai lain
**Solusi**: Check di controller bahwa role Pegawai di-filter oleh `id_pegawai`

---

## 📄 LISENSI & CATATAN

- Sistem ini dibuat khusus untuk PT Digital Solution
- Sesuaikan deployment dengan regulasi perpajakan Indonesia 2026
- Backup database sebelum batch processing
- Audit trail semua action penting di logging

---

**Dibuat oleh**: Backend Development Team
**Tanggal**: Februari 2026
**Version**: 1.0
