# DOKUMENTASI SISTEM ROLE & PERMISSION
## PT Digital Solution - Sistem Penggajian

---

## 📋 DAFTAR ISI
1. [Pengenalan Role & Permission](#pengenalan-role-permission)
2. [Struktur Role](#struktur-role)
3. [Permission List](#permission-list)
4. [Role Hierarchy](#role-hierarchy)
5. [Implementasi & Penggunaan](#implementasi-penggunaan)
6. [Best Practices](#best-practices)

---

## 🔐 PENGENALAN ROLE & PERMISSION

### Apa itu Role?
**Role** adalah sebuah himpunan **permission** yang menentukan apa yang bisa dilakukan oleh seorang user dalam sistem.

### Apa itu Permission?
**Permission** adalah akses spesifik terhadap sebuah fungsi atau resource dalam sistem (misal: lihat gaji, edit absensi, approve lembur).

### Relasi Role - Permission
```
User → Role → Permissions
         ↓
   Grup permission yang related
```

Satu user memiliki **satu role**, dan satu role memiliki **banyak permission**.

---

## 👥 STRUKTUR ROLE

### 1️⃣ ADMIN HRD
**Deskripsi**: Administrator HR - mengelola semua aspek HR dan gaji

**Akses**:
- ✅ Semua permission di sistem
- ✅ Kelola semua data pegawai
- ✅ Edit perhitungan gaji
- ✅ Buat & edit tunjangan, potongan, PTKP
- ✅ Approve semua proses
- ✅ Generate laporan
- ✅ Export data

**Contoh User**: 
- Staff HR
- Payroll Officer
- HR Manager

**Use Case**:
```
- Input gaji baru pegawai
- Edit data absensi jika ada koreksi
- Buat perhitungan gaji bulanan
- Setup tunjangan dan potongan
- Monitor semua permission di sistem
```

---

### 2️⃣ MANAGER
**Deskripsi**: Manager - mengelola departemen dan team, approve absensi/lembur

**Akses**:
- ✅ Lihat data penggajian
- ✅ Lihat data absensi (departemen)
- ✅ Approve absensi pegawai team-nya
- ✅ Lihat data lembur
- ✅ Approve lembur pegawai team-nya
- ✅ Lihat laporan absensi & lembur
- ✅ Lihat struktur departemen

**Permission yang dimiliki**:
- `gaji.view` - Lihat gaji
- `gaji.print_slip` - Print slip gaji
- `absensi.view` - Lihat absensi
- `absensi.approve` - Approve absensi
- `lembur.view` - Lihat lembur
- `lembur.approve` - Approve lembur
- `pegawai.view` - Lihat data pegawai
- `laporan.view` - Lihat laporan
- `laporan.absensi` - Lihat laporan absensi
- `laporan.lembur` - Lihat laporan lembur
- `departemen.view` - Lihat departemen
- `jabatan.view` - Lihat jabatan

**Contoh User**: 
- Department Head (Kepala IT, Kepala Sales, dll)
- Team Lead

**Use Case**:
```
- Monitoring absensi team
- Approve lembur yang diajukan pegawai
- Lihat slip gaji untuk approval
- Generate laporan tim
```

---

### 3️⃣ DIREKTUR
**Deskripsi**: Direktur - monitoring dan approval gaji

**Akses**:
- ✅ Lihat perhitungan gaji
- ✅ Approve perhitungan gaji
- ✅ Print slip gaji
- ✅ Lihat laporan komprehensif (gaji, absensi, lembur)
- ✅ Export laporan ke Excel/PDF
- ✅ Lihat struktur organisasi

**Permission yang dimiliki**:
- `gaji.view` - Lihat gaji
- `gaji.approve` - Approve gaji
- `gaji.print_slip` - Print slip gaji
- `laporan.view` - Lihat laporan
- `laporan.gaji` - Lihat laporan gaji
- `laporan.absensi` - Lihat laporan absensi
- `laporan.lembur` - Lihat laporan lembur
- `laporan.export` - Export laporan
- `pegawai.view` - Lihat pegawai
- `departemen.view` - Lihat departemen
- `jabatan.view` - Lihat jabatan

**Contoh User**: 
- Direktur Utama
- Direktur Operasional
- CFO

**Use Case**:
```
- Final approval perhitungan gaji
- Monitoring pengeluaran gaji
- Lihat laporan finansial HR
- Export gaji untuk proses transfer
```

---

### 4️⃣ PEGAWAI
**Deskripsi**: Pegawai - lihat data pribadi dan slip gaji

**Akses**:
- ✅ Lihat slip gaji sendiri
- ✅ Print slip gaji sendiri
- ✅ Lihat absensi sendiri
- ✅ Lihat lembur yang pernah dikerjakan
- ✅ Lihat data pribadi

**Permission yang dimiliki**:
- `gaji.view_own` - Lihat gaji sendiri
- `gaji.print_slip` - Print slip gaji
- `absensi.view_own` - Lihat absensi sendiri
- `lembur.view_own` - Lihat lembur sendiri
- `pegawai.view` - Lihat data pegawai (semua, tapi hanya lihat)

**Contoh User**: 
- Semua karyawan tetap
- Semua staff
- Semua engineer

**Use Case**:
```
- Lihat gaji dan slip gaji bulanan
- Melihat riwayat absensi
- Melihat riwayat lembur yang sudah dikerjakan
- Download slip gaji untuk keperluan administrasi
```

---

## 📑 PERMISSION LIST

### Kategori: PENGGAJIAN
| Permission | Deskripsi | Role |
|-----------|-----------|------|
| `gaji.view` | Lihat data penggajian semua pegawai | Admin HRD, Manager, Direktur |
| `gaji.view_own` | Lihat slip gaji sendiri | Pegawai |
| `gaji.create` | Buat perhitungan gaji | Admin HRD |
| `gaji.edit` | Edit perhitungan gaji | Admin HRD |
| `gaji.delete` | Hapus perhitungan gaji | Admin HRD |
| `gaji.approve` | Approve perhitungan gaji | Admin HRD, Direktur |
| `gaji.print_slip` | Print slip gaji | Admin HRD, Manager, Direktur, Pegawai |

### Kategori: ABSENSI
| Permission | Deskripsi | Role |
|-----------|-----------|------|
| `absensi.view` | Lihat data absensi semua pegawai | Admin HRD, Manager |
| `absensi.view_own` | Lihat absensi sendiri | Pegawai |
| `absensi.create` | Input absensi | Admin HRD |
| `absensi.edit` | Edit absensi | Admin HRD |
| `absensi.delete` | Hapus absensi | Admin HRD |
| `absensi.approve` | Approve absensi | Admin HRD, Manager |

### Kategori: LEMBUR
| Permission | Deskripsi | Role |
|-----------|-----------|------|
| `lembur.view` | Lihat data lembur semua | Admin HRD, Manager |
| `lembur.view_own` | Lihat lembur sendiri | Pegawai |
| `lembur.create` | Input lembur | Admin HRD |
| `lembur.edit` | Edit lembur | Admin HRD |
| `lembur.delete` | Hapus lembur | Admin HRD |
| `lembur.approve` | Approve lembur | Admin HRD, Manager |

### Kategori: PEGAWAI
| Permission | Deskripsi | Role |
|-----------|-----------|------|
| `pegawai.view` | Lihat data pegawai | Admin HRD, Manager, Direktur, Pegawai |
| `pegawai.create` | Tambah pegawai baru | Admin HRD |
| `pegawai.edit` | Edit data pegawai | Admin HRD |
| `pegawai.delete` | Hapus pegawai | Admin HRD |

### Kategori: TUNJANGAN
| Permission | Deskripsi | Role |
|-----------|-----------|------|
| `tunjangan.view` | Lihat data tunjangan | Admin HRD |
| `tunjangan.create` | Buat tunjangan baru | Admin HRD |
| `tunjangan.edit` | Edit tunjangan | Admin HRD |
| `tunjangan.delete` | Hapus tunjangan | Admin HRD |
| `tunjangan.assign` | Berikan tunjangan ke pegawai | Admin HRD |

### Kategori: POTONGAN
| Permission | Deskripsi | Role |
|-----------|-----------|------|
| `potongan.view` | Lihat data potongan | Admin HRD |
| `potongan.create` | Buat potongan baru | Admin HRD |
| `potongan.edit` | Edit potongan | Admin HRD |
| `potongan.delete` | Hapus potongan | Admin HRD |
| `potongan.assign` | Berikan potongan ke pegawai | Admin HRD |

### Kategori: MASTER DATA
| Permission | Deskripsi | Role |
|-----------|-----------|------|
| `departemen.view` | Lihat departemen | Admin HRD, Manager, Direktur, Pegawai |
| `departemen.create` | Buat departemen | Admin HRD |
| `departemen.edit` | Edit departemen | Admin HRD |
| `departemen.delete` | Hapus departemen | Admin HRD |
| `jabatan.view` | Lihat jabatan | Admin HRD, Manager, Direktur, Pegawai |
| `jabatan.create` | Buat jabatan | Admin HRD |
| `jabatan.edit` | Edit jabatan | Admin HRD |
| `jabatan.delete` | Hapus jabatan | Admin HRD |
| `ptkp.view` | Lihat PTKP | Admin HRD |
| `ptkp.edit` | Edit PTKP | Admin HRD |

### Kategori: LAPORAN
| Permission | Deskripsi | Role |
|-----------|-----------|------|
| `laporan.view` | Lihat laporan | Admin HRD, Manager, Direktur |
| `laporan.gaji` | Lihat laporan gaji | Admin HRD, Direktur |
| `laporan.absensi` | Lihat laporan absensi | Admin HRD, Manager, Direktur |
| `laporan.lembur` | Lihat laporan lembur | Admin HRD, Manager, Direktur |
| `laporan.export` | Export laporan | Admin HRD, Direktur |

---

## 🏛️ ROLE HIERARCHY

```
┌──────────────────────────────────────────────────────────┐
│                       ADMIN HRD                          │
│      (Semua akses, kelola semua aspek sistem)            │
├──────────────────────────────────────────────────────────┤
│  - Kelola pegawai & gaji                                 │
│  - Kalkulasi & edit perhitungan gaji                     │
│  - Input absensi & lembur                                │
│  - Setup tunjangan & potongan                            │
│  - Approve semua proses                                  │
│  - Export & backup data                                  │
└──────────────────────────────────────────────────────────┘
                           △
                        △  │  △
                       /   │   \
                      /    │    \
                     /     │     \
            ┌───────/──────┴──────\────────┐
            │      /       │       \       │
        ┌─────────┐   ┌──────────┐   ┌──────────┐
        │ MANAGER │   │DIREKTUR  │   │ PEGAWAI  │
        ├─────────┤   ├──────────┤   ├──────────┤
        │ - View  │   │ - View   │   │ - View   │
        │ - Appr. │   │ - Approve│   │ - Own    │
        │   (Tim) │   │   (Gaji) │   │ - Slip   │
        └─────────┘   └──────────┘   └──────────┘
```

---

## 💻 IMPLEMENTASI & PENGGUNAAN

### 1. Setup Database Migration

Pastikan tabel `role`, `permission`, dan `role_permission` sudah ada:

```php
// database/migrations/xxxx_xx_xx_create_roles_permissions_table.php

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
});

// Update tabel users untuk menambahkan id_role
Schema::table('users', function (Blueprint $table) {
    $table->unsignedBigInteger('id_role')->nullable();
    $table->foreign('id_role')->references('id_role')->on('role')->onDelete('set null');
});
```

### 2. Run Seeder

```bash
php artisan db:seed --class=RoleAndPermissionSeeder
```

### 3. Menggunakan Permission dalam Code

#### Mengecek Permission Individual
```php
// Dalam Controller atau Service
if (auth()->user()->hasPermission('gaji.view')) {
    // Lakukan sesuatu
}
```

#### Mengecek Multiple Permission (OR)
```php
// User harus memiliki salah satu dari permission ini
if (auth()->user()->hasAnyPermission(['gaji.view', 'gaji.create'])) {
    // Lakukan sesuatu
}
```

#### Mengecek Multiple Permission (AND)
```php
// User harus memiliki semua permission ini
if (auth()->user()->hasAllPermissions(['gaji.view', 'gaji.edit', 'gaji.approve'])) {
    // Lakukan sesuatu
}
```

#### Mengecek Role
```php
// Cek role spesifik
if (auth()->user()->hasRole('Admin HRD')) {
    // Lakukan sesuatu
}

// Cek salah satu role
if (auth()->user()->hasAnyRole(['Admin HRD', 'Manager'])) {
    // Lakukan sesuatu
}
```

### 4. Menggunakan Middleware

#### Setup Middleware di app/Http/Kernel.php
```php
protected $routeMiddleware = [
    // ... middleware lainnya
    'permission' => \App\Http\Middleware\CheckPermission::class,
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

#### Menggunakan Permission Middleware
```php
// routes/api.php

// Hanya user dengan permission gaji.view bisa akses
Route::get('/gaji', [SalaryController::class, 'index'])
    ->middleware('permission:gaji.view');

// Hanya user dengan permission gaji.create bisa akses
Route::post('/gaji', [SalaryController::class, 'store'])
    ->middleware('permission:gaji.create');
```

#### Menggunakan Role Middleware
```php
// Hanya Admin HRD dan Direktur bisa akses
Route::post('/gaji/approve', [SalaryController::class, 'approve'])
    ->middleware('role:Admin HRD,Direktur');
```

### 5. Contoh Controller dengan Permission Checks

```php
<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Services\SalaryCalculationService;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    protected $salaryService;

    public function __construct(SalaryCalculationService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    /**
     * List gaji
     */
    public function index(Request $request)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.view')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat data gaji'
            ], 403);
        }

        $gaji = Penggajian::all();
        return response()->json(['data' => $gaji]);
    }

    /**
     * Lihat slip gaji
     */
    public function show(Request $request, $penggajianId)
    {
        $penggajian = Penggajian::find($penggajianId);

        // Pegawai hanya bisa lihat gaji sendiri
        if ($request->user()->hasRole('Pegawai')) {
            if ($penggajian->id_pegawai !== $request->user()->id_pegawai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda hanya bisa melihat slip gaji Anda sendiri'
                ], 403);
            }
        }

        return response()->json(['data' => $penggajian]);
    }

    /**
     * Hitung gaji
     */
    public function calculate(Request $request)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.create')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk membuat perhitungan gaji'
            ], 403);
        }

        // Logic kalkulasi gaji
        // ...
    }

    /**
     * Approve gaji
     */
    public function approve(Request $request, $penggajianId)
    {
        // Check permission
        if (!$request->user()->hasPermission('gaji.approve')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk approve gaji'
            ], 403);
        }

        // Logic approve gaji
        // ...
    }
}
```

### 6. Setup Auth User dengan Role

```php
// Saat login, assign role ke user
// app/Http/Controllers/AuthController.php

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        
        // Pastikan user memiliki role
        if (!$user->id_role) {
            // Set default role ke 'Pegawai'
            $pegawaiRole = Role::where('nama_role', 'Pegawai')->first();
            $user->id_role = $pegawaiRole->id_role;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Invalid credentials'
    ], 401);
}
```

---

## 🎯 BEST PRACTICES

### 1. Permission Naming Convention
```
Format: resource.action

Contoh:
- gaji.view (lihat gaji)
- gaji.create (buat gaji)
- gaji.edit (edit gaji)
- gaji.delete (hapus gaji)
- gaji.approve (approve gaji)
```

### 2. Role Assignment
```php
// Assign role ke user saat onboarding
$user = User::create([
    'name' => 'Ahmad',
    'email' => 'ahmad@example.com',
    'password' => bcrypt('password'),
    'id_role' => Role::where('nama_role', 'Pegawai')->first()->id_role
]);
```

### 3. Menggunakan Scopes untuk Query
```php
// Seeder atau seeding data user
// User Admin HRD
User::create([
    'name' => 'Admin HR',
    'email' => 'admin@digitalsolution.com',
    'password' => bcrypt('password123'),
    'id_role' => Role::where('nama_role', 'Admin HRD')->first()->id_role
]);

// User Manager
User::create([
    'name' => 'Budi (Manager IT)',
    'email' => 'budi@digitalsolution.com',
    'password' => bcrypt('password123'),
    'id_role' => Role::where('nama_role', 'Manager')->first()->id_role
]);

// User Regular Pegawai
User::create([
    'name' => 'Rina',
    'email' => 'rina@digitalsolution.com',
    'password' => bcrypt('password123'),
    'id_role' => Role::where('nama_role', 'Pegawai')->first()->id_role
]);
```

### 4. Audit Trail
```php
// Catat setiap action yang sensitive
public function store(Request $request)
{
    // ... validation & logic

    // Log action
    Log::info('User ' . auth()->user()->name . ' membuat gaji baru', [
        'pegawai_id' => $pegawai->id_pegawai,
        'periode' => $request->periode
    ]);
}
```

### 5. Policy Pattern (Advanced)
```php
// app/Policies/PenggajianPolicy.php
public function view(User $user, Penggajian $penggajian)
{
    // Pegawai hanya bisa lihat gaji sendiri
    if ($user->hasRole('Pegawai')) {
        return $penggajian->id_pegawai === $user->id_pegawai;
    }

    // Admin dan Manager bisa lihat semua
    return $user->hasAnyRole(['Admin HRD', 'Manager', 'Direktur']);
}

// Penggunaan di controller
public function show($penggajianId)
{
    $penggajian = Penggajian::find($penggajianId);
    $this->authorize('view', $penggajian);
    
    return response()->json(['data' => $penggajian]);
}
```

---

## 📊 SUMMARY ROLE & PERMISSION

### Tabel Akses per Role

| Fitur | Admin HRD | Manager | Direktur | Pegawai |
|-------|:---------:|:-------:|:--------:|:-------:|
| View Gaji (semua) | ✅ | ✅ | ✅ | ❌ |
| View Gaji (own) | ✅ | ✅ | ✅ | ✅ |
| Create Gaji | ✅ | ❌ | ❌ | ❌ |
| Edit Gaji | ✅ | ❌ | ❌ | ❌ |
| Approve Gaji | ✅ | ❌ | ✅ | ❌ |
| Input Absensi | ✅ | ❌ | ❌ | ❌ |
| Approve Absensi | ✅ | ✅ | ❌ | ❌ |
| Input Lembur | ✅ | ❌ | ❌ | ❌ |
| Approve Lembur | ✅ | ✅ | ❌ | ❌ |
| Kelola Tunjangan | ✅ | ❌ | ❌ | ❌ |
| Kelola Potongan | ✅ | ❌ | ❌ | ❌ |
| View Laporan | ✅ | ✅ | ✅ | ❌ |
| Export Laporan | ✅ | ❌ | ✅ | ❌ |

---

## ✅ CHECKLIST IMPLEMENTASI

- [ ] Migration untuk role_permission sudah dibuat
- [ ] Model Role, Permission, dan traits sudah dibuat
- [ ] User model sudah update dengan id_role
- [ ] Middleware CheckPermission & CheckRole sudah dibuat
- [ ] RoleAndPermissionSeeder sudah dibuat
- [ ] Seeder sudah di-run (`php artisan db:seed --class=RoleAndPermissionSeeder`)
- [ ] Routes sudah di-setup dengan middleware
- [ ] Controllers sudah implement permission checks
- [ ] Test akses per role sudah dilakukan
- [ ] Documentation sudah disebarkan ke team

---

**Dibuat oleh**: PT Digital Solution - Backend Team
**Terakhir diupdate**: Februari 2026
