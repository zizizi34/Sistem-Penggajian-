# DOKUMENTASI SISTEM PERHITUNGAN GAJI
## PT Digital Solution

---

## 📋 DAFTAR ISI
1. [Alur Perhitungan Gaji](#alur-perhitungan-gaji)
2. [Komponen Gaji](#komponen-gaji)
3. [Detail Setiap Komponen](#detail-setiap-komponen)
4. [Contoh Perhitungan](#contoh-perhitungan)
5. [Panduan Implementasi](#panduan-implementasi)

---

## 🔄 ALUR PERHITUNGAN GAJI

### Flowchart Proses Perhitungan

```
┌─────────────────────────────────────────────────────────────────┐
│ INPUT: Pegawai ID, Periode (YYYY-MM)                            │
└──────────────────────┬──────────────────────────────────────────┘
                       │
                       ▼
          ┌────────────────────────────┐
          │  Validasi Data Pegawai     │
          │ & Periode                  │
          └────────┬───────────────────┘
                   │
        ┌──────────┼──────────┐
        │          │          │
        ▼          ▼          ▼
    ┌──────┐ ┌────────┐ ┌──────────┐
    │ GAJI │ │TUNJANGAN│ │ LEMBUR   │
    │POKOK │ │         │ │          │
    └──────┘ └────────┘ └──────────┘
        │          │          │
        │    ┌─────┴──────┬───┤
        │    │            │
        ▼    ▼            ▼
    ┌──────────────────────────────┐
    │  GAJI BRUTO                  │
    │ = Gaji Pokok + Tunjangan     │
    │   + Lembur - Absensi         │
    └──────────────┬───────────────┘
                   │
        ┌──────────┼──────────┐
        │          │          │
        ▼          ▼          ▼
    ┌─────────┐┌──────────┐┌──────────┐
    │POTONGAN ││PAJAK PPh ││GAJI BRUTO│
    │NON-PAJAK││    21    ││FINAL     │
    └─────────┘└──────────┘└──────────┘
        │          │          │
        └──────────┼──────────┘
                   ▼
    ┌──────────────────────────────┐
    │  GAJI BERSIH                 │
    │ = Gaji Bruto - Potongan      │
    │   - Pajak PPh 21             │
    └──────────────┬───────────────┘
                   │
                   ▼
        ┌────────────────────────────┐
        │ OUTPUT: Detail Perhitungan  │
        └────────────────────────────┘
```

---

## 💰 KOMPONEN GAJI

### Formula Perhitungan Gaji Bersih

```
GAJI BERSIH = GAJI POKOK + TUNJANGAN + LEMBUR - ABSENSI - POTONGAN - PAJAK PPh 21
```

### Breakdown Komponen

| Komponen | Jenis | Rumus |
|----------|-------|-------|
| **Gaji Pokok** | Tetap | Dari master data pegawai |
| **Tunjangan** | Variabel | Σ (Tunjangan per pegawai) |
| **Lembur** | Variabel | Hitung dari data lembur × rate |
| **Absensi** | Pengurangan | Keterlambatan + Alpha |
| **Potongan** | Variabel | Σ (Potongan per pegawai) |
| **Pajak PPh 21** | Variabel | Hitung dari gaji bruto & PTKP |

---

## 📊 DETAIL SETIAP KOMPONEN

### 1️⃣ GAJI POKOK

**Sumber**: Master data `pegawai.gaji_pokok`

**Pemilihan Gaji Pokok**:
- Berdasarkan jabatan dan pengalaman
- Disesuaikan dengan range gaji dari tabel `jabatan` (min_gaji, max_gaji)
- Disimpan di record pegawai

```php
$gaji_pokok = $pegawai->gaji_pokok;
```

---

### 2️⃣ ABSENSI

**Sumber**: Tabel `absensi` (berisi jam_masuk, jam_pulang, status)

#### Status Absensi:
| Status | Keterangan | Pengaruh Gaji |
|--------|-----------|---------------|
| **hadir** | Masuk normal | Tidak ada potongan (jika tepat waktu) |
| **hadir_terlambat** | Masuk terlambat | Potongan Rp 50.000 per kali |
| **izin** | Dengan surat izin | Tidak ada potongan |
| **sakit** | Dengan surat dokter | Tidak ada potongan (max 12 hari/tahun) |
| **alpha** | Tidak masuk, tidak ada keterangan | Potongan Rp 100.000 per hari |

#### Perhitungan Absensi:
```
Hari Kerja Seharusnya = Total hari kerja (Senin-Jumat) dalam bulan

Hari Hadir = COUNT(status = 'hadir' AND jam_masuk ≤ '08:00:00')

Hari Terlambat = COUNT(status = 'hadir' AND jam_masuk > '08:00:00')

Hari Izin = COUNT(status = 'izin')

Hari Sakit = COUNT(status = 'sakit')

Hari Alpha = Hari Kerja Seharusnya - (Hadir + Izin + Sakit)

Potongan Absensi = (Hari Terlambat × 50.000) + (Hari Alpha × 100.000)
```

**Contoh**:
- Hari kerja bulan Januari 2026: 23 hari (Senin-Jumat)
- Hadir: 20 hari
- Terlambat: 2 kali (Rp 50.000 × 2 = Rp 100.000)
- Alpha: 1 hari (Rp 100.000)
- **Total potongan absensi: Rp 200.000**

---

### 3️⃣ TUNJANGAN

**Sumber**: Tabel `pegawai_tunjangan` yang menghubungkan pegawai dengan tunjangan

#### Jenis-Jenis Tunjangan (Contoh untuk Software House):
| Tunjangan | Nominal | Keterangan |
|-----------|---------|-----------|
| Tunjangan Transport | Rp 500.000 | Per bulan, untuk semua pegawai |
| Tunjangan Makan | Rp 300.000 | Per bulan |
| Tunjangan Kesehatan | Rp 200.000 | Per bulan |
| Tunjangan Performa | Rp 1.000.000 | Untuk engineer/developer |
| Bonus Proyek | Variabel | Berdasarkan pencapaian |

#### Perhitungan:
```
Total Tunjangan = Σ (nominal tunjangan pegawai)
```

**Contoh**:
- Seorang developer menerima:
  - Transport: Rp 500.000
  - Makan: Rp 300.000
  - Kesehatan: Rp 200.000
  - Performa: Rp 1.000.000
  - **Total Tunjangan: Rp 2.000.000**

---

### 4️⃣ LEMBUR (OVERTIME)

**Sumber**: Tabel `lembur` (berisi jam_mulai, jam_selesai, durasi)

#### Rate Lembur:
| Keterangan | Rate | Rumus |
|-----------|------|-------|
| Jam pertama | 1.5× | 1 jam × upah/jam × 1.5 |
| Jam berikutnya | 2× | sisa jam × upah/jam × 2 |

#### Perhitungan Upah Per Jam:
```
Upah Per Jam = Gaji Pokok / 173 jam kerja per bulan

Catatan: 173 jam adalah standar jam kerja per bulan untuk 8 jam/hari, 5 hari/minggu
```

#### Rumus Perhitungan Lembur:
```
JIKA durasi ≤ 1 jam:
  Uang Lembur = durasi × (Gaji Pokok / 173) × 1.5

JIKA durasi > 1 jam:
  Uang Lembur = (1 × (Gaji Pokok / 173) × 1.5) + ((durasi - 1) × (Gaji Pokok / 173) × 2)
```

**Contoh Perhitungan**:
```
Gaji Pokok: Rp 12.000.000
Upah Per Jam: Rp 12.000.000 / 173 = Rp 69.364

Lembur 1 jam:
  = 1 × 69.364 × 1.5 = Rp 104.046

Lembur 3 jam (1 jam pertama: 1.5×, 2 jam berikutnya: 2×):
  = (1 × 69.364 × 1.5) + (2 × 69.364 × 2)
  = 104.046 + 277.456
  = Rp 381.502

Lembur dalam bulan: 5 hari × 2 jam = 10 jam
Total Lembur = 10 × 69.364 × 1.5 = Rp 1.040.460 (jika semua hari 1.5×)
```

---

### 5️⃣ POTONGAN

**Sumber**: Tabel `pegawai_potongan` yang menghubungkan pegawai dengan potongan

#### Jenis-Jenis Potongan:
| Potongan | Nominal | Keterangan |
|----------|---------|-----------|
| Asuransi Kesehatan | Rp 200.000 | Iuran BPJS Kesehatan (peserta) |
| Asuransi Ketenagakerjaan | Rp 100.000 | BPJS Ketenagakerjaan |
| Zakat | Rp 150.000 | Iuran zakat (sukarela) |
| Cicilan Pinjaman | Rp 500.000 | Hutang ke koperasi/bank |
| Denda Disiplin | Rp 100.000 | Jika ada pelanggaran |

#### Perhitungan:
```
Total Potongan (Non-Pajak) = Σ (nominal potongan pegawai)
```

**Contoh**:
```
Asuransi Kesehatan: Rp 200.000
Cicilan Pinjaman: Rp 500.000
Zakat: Rp 150.000
Total Potongan: Rp 850.000
```

---

### 6️⃣ PAJAK PENGHASILAN (PPh 21)

**Sumber**: Perhitungan dari Gaji Bruto berdasarkan PTKP

#### Status PTKP (Per Tahun):
| Kode | Deskripsi | Nominal PTKP |
|------|-----------|--------------|
| TK/0 | Lajang, tidak ada tanggungan | Rp 54.000.000 |
| TK/1 | Lajang, 1 tanggungan | Rp 58.500.000 |
| TK/3 | Lajang, 3 tanggungan | Rp 67.500.000 |
| K/0 | Kawin, tidak ada tanggungan | Rp 58.500.000 |
| K/1 | Kawin, 1 tanggungan | Rp 63.000.000 |
| K/3 | Kawin, 3 tanggungan | Rp 81.000.000 |

#### Rumus PPh 21:

```
LANGKAH 1: Hitung Gaji Bruto Tahunan
  Gaji Bruto Tahunan = (Gaji Pokok + Tunjangan + Lembur - Absensi × 12)

LANGKAH 2: Hitung Penghasilan Kena Pajak (PKP)
  PKP = Gaji Bruto Tahunan - PTKP

LANGKAH 3: Hitung Pajak dengan Tarif Progresif
  JIKA PKP ≤ Rp 60.000.000:
    Pajak Tahunan = PKP × 5%
  
  JIKA Rp 60.000.000 < PKP ≤ Rp 250.000.000:
    Pajak Tahunan = (60.000.000 × 5%) + ((PKP - 60.000.000) × 15%)
  
  JIKA Rp 250.000.000 < PKP ≤ Rp 500.000.000:
    Pajak Tahunan = (60.000.000 × 5%) + ((250.000.000 - 60.000.000) × 15%) 
                    + ((PKP - 250.000.000) × 25%)
  
  (Tarif berlanjut untuk bracket lebih tinggi)

LANGKAH 4: Hitung PPh 21 Per Bulan
  PPh 21 Per Bulan = Pajak Tahunan / 12
```

**Contoh Perhitungan PPh 21**:

```
Data Pegawai:
- Gaji Pokok: Rp 12.000.000
- Tunjangan: Rp 2.000.000
- Lembur: Rp 1.000.000
- Potongan Absensi: Rp 200.000
- Status PTKP: K/1 (PTKP = Rp 63.000.000)

Perhitungan:
1. Gaji Bruto Bulanan = 12.000.000 + 2.000.000 + 1.000.000 - 200.000
                      = Rp 14.800.000

2. Gaji Bruto Tahunan = Rp 14.800.000 × 12 = Rp 177.600.000

3. PKP Tahunan = Rp 177.600.000 - Rp 63.000.000 = Rp 114.600.000

4. Pajak Tahunan:
   = (60.000.000 × 5%) + ((114.600.000 - 60.000.000) × 15%)
   = 3.000.000 + 8.190.000
   = Rp 11.190.000

5. PPh 21 Per Bulan = Rp 11.190.000 / 12 = Rp 932.500
```

---

### 7️⃣ GAJI BERSIH

**Rumus**:
```
GAJI BERSIH = Gaji Pokok + Tunjangan + Lembur 
              - Potongan Absensi - Potongan Lainnya - PPh 21

ATAU:

GAJI BERSIH = Gaji Bruto - Potongan - PPh 21
```

**Contoh**:
```
Gaji Pokok:       Rp 12.000.000
Tunjangan:        + Rp 2.000.000
Lembur:           + Rp 1.000.000
                  ─────────────────
Gaji Bruto:       = Rp 15.000.000

Potongan Absensi: - Rp 200.000
Potongan Lain:    - Rp 850.000
Pajak PPh 21:     - Rp 932.500
                  ─────────────────
GAJI BERSIH:      = Rp 13.017.500
```

---

## 📐 CONTOH PERHITUNGAN LENGKAP

### Data Pegawai
```
ID Pegawai: 001
Nama: Ahmad Fauzi
Jabatan: Senior Developer
Departemen: IT
Gaji Pokok: Rp 15.000.000
Status PTKP: K/1 (Rp 63.000.000/tahun)
Periode: Januari 2026
```

### Data Tunjangan
```
- Tunjangan Transport: Rp 500.000
- Tunjangan Makan: Rp 300.000
- Tunjangan Kesehatan: Rp 200.000
- Tunjangan Performa: Rp 1.500.000
Total: Rp 2.500.000
```

### Data Absensi (Januari 2026 = 23 hari kerja)
```
- Hadir: 20 hari
- Terlambat: 2 kali (Rp 50.000 × 2 = Rp 100.000)
- Izin: 1 hari
- Sakit: 0 hari
- Alpha: 0 hari
Potongan Absensi: Rp 100.000
```

### Data Lembur
```
Lembur 5 hari, masing-masing 2 jam:
- Upah per jam = Rp 15.000.000 / 173 = Rp 86.705
- Untuk 2 jam per hari (1 jam: 1.5×, 1 jam: 2×):
  (1 × 86.705 × 1.5) + (1 × 86.705 × 2) = 130.057 + 173.410 = Rp 303.467
- Total lembur 5 hari = Rp 303.467 × 5 = Rp 1.517.335
```

### Data Potongan
```
- Asuransi Kesehatan: Rp 250.000
- Cicilan Pinjaman: Rp 500.000
- Zakat: Rp 150.000
Total Potongan: Rp 900.000
```

### PERHITUNGAN GAJI

```
1. GAJI BRUTO (Sebelum Pajak):
   = Gaji Pokok + Tunjangan + Lembur - Absensi
   = 15.000.000 + 2.500.000 + 1.517.335 - 100.000
   = Rp 18.917.335

2. PERHITUNGAN PAJAK PPh 21:
   a) Gaji Bruto Tahunan = Rp 18.917.335 × 12 = Rp 227.008.020
   b) PKP Tahunan = Rp 227.008.020 - Rp 63.000.000 = Rp 164.008.020
   c) Pajak Tahunan:
      = (60.000.000 × 5%) + ((164.008.020 - 60.000.000) × 15%)
      = 3.000.000 + 15.601.203
      = Rp 18.601.203
   d) PPh 21 Per Bulan = Rp 18.601.203 / 12 = Rp 1.550.100

3. GAJI BERSIH:
   = Gaji Bruto - Potongan - Pajak PPh 21
   = 18.917.335 - 900.000 - 1.550.100
   = Rp 16.467.235
```

### SUMMARY SLIP GAJI

```
╔═══════════════════════════════════════════════════════════════════╗
║                        SLIP GAJI                                  ║
║                   JANUARI 2026                                    ║
╠═══════════════════════════════════════════════════════════════════╣
║ PEGAWAI:  Ahmad Fauzi                    ID: 001                  ║
║ JABATAN:  Senior Developer                                        ║
║ DEPARTEMEN: IT                                                    ║
╠═══════════════════════════════════════════════════════════════════╣
║                         PENGHASILAN                                ║
║  Gaji Pokok                          Rp 15.000.000                ║
║  Tunjangan Transport         Rp 500.000                           ║
║  Tunjangan Makan             Rp 300.000                           ║
║  Tunjangan Kesehatan         Rp 200.000                           ║
║  Tunjangan Performa          Rp 1.500.000                         ║
║  Lembur (10 jam)             Rp 1.517.335                         ║
║                              ───────────────                       ║
║  TOTAL GAJI BRUTO            Rp 18.917.335                        ║
╠═══════════════════════════════════════════════════════════════════╣
║                         POTONGAN                                   ║
║  Potongan Absensi (2× terlambat)     Rp 100.000                   ║
║  Asuransi Kesehatan                  Rp 250.000                   ║
║  Cicilan Pinjaman Koperasi           Rp 500.000                   ║
║  Zakat                               Rp 150.000                   ║
║  Pajak PPh 21                        Rp 1.550.100                 ║
║                              ───────────────                       ║
║  TOTAL POTONGAN              Rp 2.550.100                         ║
╠═══════════════════════════════════════════════════════════════════╣
║ GAJI BERSIH                  Rp 16.467.235                         ║
╚═══════════════════════════════════════════════════════════════════╝
```

---

## 💻 PANDUAN IMPLEMENTASI

### Menggunakan SalaryCalculationService

```php
<?php

namespace App\Http\Controllers;

use App\Services\SalaryCalculationService;
use App\Models\Pegawai;

class SalaryController extends Controller
{
    protected $salaryService;

    public function __construct(SalaryCalculationService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    /**
     * Hitung gaji single pegawai
     */
    public function calculateSingle($pegawaiId, $periode)
    {
        $pegawai = Pegawai::find($pegawaiId);
        
        $result = $this->salaryService->calculateMonthlySalary($pegawai, $periode);
        
        if ($result['status'] === 'success') {
            // Simpan ke database
            $this->salaryService->saveSalaryCalculation($pegawai, $periode, $result);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Gaji berhasil dihitung',
                'data' => $result
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => $result['message']
        ], 422);
    }

    /**
     * Hitung gaji semua pegawai (batch)
     */
    public function calculateBatch($periode)
    {
        $pegawais = Pegawai::where('status_pegawai', 'aktif')->get();
        
        $results = [];
        foreach ($pegawais as $pegawai) {
            $result = $this->salaryService->calculateMonthlySalary($pegawai, $periode);
            
            if ($result['status'] === 'success') {
                $this->salaryService->saveSalaryCalculation($pegawai, $periode, $result);
            }
            
            $results[] = $result;
        }
        
        return response()->json([
            'status' => 'success',
            'total' => count($results),
            'data' => $results
        ]);
    }

    /**
     * Lihat detail slip gaji
     */
    public function showSlip($penggajianId)
    {
        $penggajian = Penggajian::with('pegawai')->find($penggajianId);
        
        return response()->json([
            'status' => 'success',
            'data' => $penggajian
        ]);
    }
}
```

### Route untuk Perhitungan Gaji

```php
// routes/api.php

// Kalkulasi gaji single pegawai
Route::post('/gaji/hitung/{pegawaiId}/{periode}', [SalaryController::class, 'calculateSingle']);

// Kalkulasi gaji batch
Route::post('/gaji/batch/{periode}', [SalaryController::class, 'calculateBatch']);

// Lihat slip gaji
Route::get('/gaji/{penggajianId}', [SalaryController::class, 'showSlip']);
```

---

## ✅ CHECKLIST IMPLEMENTASI

- [ ] Service `SalaryCalculationService` sudah dibuat
- [ ] Model `Penggajian` sudah memiliki relasi ke `Pegawai`
- [ ] Data `pegawai_tunjangan` sudah terisi
- [ ] Data `pegawai_potongan` sudah terisi
- [ ] Data `absensi` sudah valid
- [ ] Data `lembur` sudah valid
- [ ] PTKP status sudah diisi di master `ptkp_status`
- [ ] Endpoint API sudah dibuat di controller
- [ ] Testing / QA telah dilakukan
- [ ] Dokumentasi user sudah siap

---

## 📝 CATATAN PENTING

1. **Hari Kerja**: Dihitung dari Senin-Jumat (5 hari kerja/minggu)
2. **Upload Absensi**: Harus dilakukan tepat waktu setiap hari
3. **Pembayaran Lembur**: Diperhitungkan otomatis dari durasi lembur
4. **Pajak PPh 21**: Dihitung dengan tarif progresif sesuai regulasi Indonesia 2024
5. **Periode Gaji**: Format YYYY-MM (contoh: 2026-01)
6. **Review Data**: Selalu review data sebelum approve slip gaji
7. **Backup**: Selalu backup database sebelum proses gajian massal

---

**Dibuat oleh**: PT Digital Solution - Backend Team
**Terakhir diupdate**: Februari 2026
