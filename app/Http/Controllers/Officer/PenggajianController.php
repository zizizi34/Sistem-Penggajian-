<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use App\Models\Pegawai;
use App\Services\SalaryCalculationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Officer/PenggajianController
 *
 * Hanya dapat diakses oleh officer dari departemen Human Resources.
 * HR Officer bertanggung jawab menghitung & mengelola penggajian
 * SELURUH pegawai perusahaan (semua departemen).
 */
class PenggajianController extends Controller
{
    /** Nama bulan Indonesia */
    private static array $bulanId = [
        1=>'Januari', 2=>'Februari', 3=>'Maret',    4=>'April',
        5=>'Mei',     6=>'Juni',     7=>'Juli',      8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November', 12=>'Desember',
    ];

    private function toPeriodeLabel(string $periodeRaw): string
    {
        $dt = Carbon::createFromFormat('Y-m', $periodeRaw);
        return self::$bulanId[(int)$dt->format('n')] . ' ' . $dt->format('Y');
    }

    /**
     * Tampilkan daftar penggajian SEMUA pegawai (lintas departemen).
     * HR bisa filter per periode dan per departemen.
     */
    public function index(Request $request)
    {
        // HR melihat semua penggajian seluruh departemen
        $query = Penggajian::with(['pegawai', 'pegawai.jabatan', 'pegawai.departemen']);

        // Filter berdasarkan periode
        $periodeFilter = null;
        $periodeLabel  = null;

        if ($request->filled('periode')) {
            $periodeRaw   = $request->periode; // "2026-03"
            $periodeLabel = $this->toPeriodeLabel($periodeRaw);
            $query->where('periode', $periodeLabel);
            $periodeFilter = $periodeRaw;
        }

        // Filter opsional berdasarkan departemen (untuk kemudahan HR)
        if ($request->filled('departemen_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen_id);
            });
        }

        $penggajian = $query->orderBy('created_at', 'desc')->get();

        // Daftar departemen untuk filter dropdown
        $departemens = \App\Models\Departemen::orderBy('nama_departemen')->get();

        return view('officer.penggajian.index', compact(
            'penggajian', 'periodeFilter', 'periodeLabel', 'departemens'
        ));
    }

    /**
     * Detail penggajian — HR bisa lihat siapapun.
     */
    public function show($id)
    {
        $penggajian = Penggajian::with(['pegawai', 'pegawai.jabatan', 'pegawai.departemen'])
            ->findOrFail($id);

        $bulanMap = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];

        $startDate = now()->startOfMonth();
        $parts = explode(' ', $penggajian->periode);
        if (count($parts) == 2) {
            $month = $bulanMap[$parts[0]] ?? now()->month;
            $year = $parts[1];
            $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        }
        $endDate = $startDate->copy()->endOfMonth();

        $joinDate = $penggajian->pegawai->tgl_masuk ? \Carbon\Carbon::parse($penggajian->pegawai->tgl_masuk) : $penggajian->pegawai->created_at;
        $calcStart = $startDate->copy();
        if ($joinDate && $joinDate->greaterThan($calcStart)) { $calcStart = $joinDate; }
        $calcEnd = $endDate->copy();
        if ($calcEnd->isFuture()) { $calcEnd = \Carbon\Carbon::now(); }

        $absences = \App\Models\Absensi::where('id_pegawai', $penggajian->id_pegawai)
            ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $present = 0; $izin = 0; $sakit = 0; $alphaDb = 0; $lateCount = 0;
        $jadwal = $penggajian->pegawai->departemen->jadwalKerja ?? null;
        $standardIn = $jadwal ? $jadwal->jam_masuk : '08:00:00';
        $tolerance = $jadwal ? $jadwal->toleransi_terlambat : 0;

        foreach ($absences as $ab) {
            $statusLower = strtolower($ab->status);
            if (in_array($statusLower, ['hadir', 'terlambat', 'lupa absen pulang', 'lembur', 'lembur tetapi lupa absen pulang'])) {
                $present++;
                if ($ab->jam_masuk) {
                    $entryTime = strtotime($ab->jam_masuk);
                    $limitTime = strtotime($standardIn) + ($tolerance * 60);
                    if ($entryTime > $limitTime) { $lateCount++; }
                }
            } elseif ($statusLower === 'izin') { $izin++; }
            elseif ($statusLower === 'sakit') { $sakit++; }
            elseif ($statusLower === 'alpha') { $alphaDb++; }
        }

        $salaryService = app(\App\Services\SalaryCalculationService::class);
        $reflection = new \ReflectionClass($salaryService);
        $method = $reflection->getMethod('countWorkingDays');
        $method->setAccessible(true);
        $workingDays = $method->invoke($salaryService, $calcStart, $calcEnd, $jadwal ? $jadwal->hari : 'Senin-Jumat');

        $totalRecordedDays = $present + $izin + $sakit + $alphaDb;
        $missingDays = max(0, $workingDays - $totalRecordedDays);
        $totalAlpha = $alphaDb + $missingDays;

        $stats = [
            'hadir' => $present,
            'izin'  => $izin + $sakit,
            'alpha' => $totalAlpha,
        ];

        // Singkronisasi Gaji Pokok untuk tampilan rincian (sesuaikan dengan SalaryCalculationService)
        $baseSalaryCalculated = $penggajian->gaji_pokok;
        if ($penggajian->pegawai->jabatan) {
            if ($baseSalaryCalculated < $penggajian->pegawai->jabatan->min_gaji || $baseSalaryCalculated > $penggajian->pegawai->jabatan->max_gaji) {
                $baseSalaryCalculated = $penggajian->pegawai->jabatan->min_gaji;
            }
        }

        $dailySalary = $baseSalaryCalculated / 22;
        $potonganAlpha = $totalAlpha * $dailySalary;
        $potonganTelat = $lateCount * 25000;
        
        $potonganKhusus = 0;
        $potonganLain = [];
        $pKhususDb = $penggajian->pegawai->potongans ?? collect([]);
        foreach($pKhususDb as $pk) {
            $potonganKhusus += $pk->nominal;
            $potonganLain[] = ['nama' => $pk->nama_potongan, 'nominal' => $pk->nominal];
        }

        $rincianPotongan = [
            'alpha_count' => $totalAlpha,
            'alpha_nominal' => round($potonganAlpha, 2),
            'telat_count' => $lateCount,
            'telat_nominal' => $potonganTelat,
            'lain_lain' => $potonganLain,
            'total_lain' => $potonganKhusus
        ];

        return view('officer.penggajian.show', compact('penggajian', 'stats', 'rincianPotongan'));
    }

    /**
     * Hitung dan simpan penggajian bulanan SEMUA departemen.
     * HR bisa hitung semua pegawai aktif sekaligus, atau per departemen tertentu.
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'periode'       => 'required|regex:/^\d{4}-\d{2}$/',
            'departemen_id' => 'nullable|exists:departemen,id_departemen',
        ]);

        $periodeRaw      = $request->periode;
        $periodeLabel    = $this->toPeriodeLabel($periodeRaw);
        $periodeCarbon   = Carbon::createFromFormat('Y-m', $periodeRaw);
        
        // 1. CEK MASA DEPAN
        if ($periodeCarbon->isFuture()) {
            return redirect()->back()->with('error', "Gaji untuk periode {$periodeLabel} belum bisa diproses karena waktu belum tiba.");
        }

        // 2. CEK DATA ABSENSI
        $hasAbsensi = \App\Models\Absensi::where('tanggal_absensi', 'like', $periodeRaw . '%')->exists();
        if (!$hasAbsensi) {
            return redirect()->back()->with('error', "Belum ada data absensi/kehadiran untuk periode {$periodeLabel}, sehingga gaji belum bisa dihitung.");
        }

        $tanggalTransfer = $periodeCarbon->endOfMonth()->format('Y-m-d');
        $salaryService = app(SalaryCalculationService::class);

        // Ambil pegawai aktif — semua departemen, atau filter departemen tertentu
        $query = Pegawai::where('status_pegawai', 'aktif');
        if ($request->filled('departemen_id')) {
            $query->where('id_departemen', $request->departemen_id);
        }
        $pegawais = $query->get();

        $countBerhasil = 0;
        $countUpdated  = 0;
        $countPaid     = 0;
        $countGagal    = 0;

        foreach ($pegawais as $pegawai) {
            // Cek apakah sudah ada untuk periode ini
            $existing = Penggajian::where('id_pegawai', $pegawai->id_pegawai)
                ->where('periode', $periodeLabel)
                ->first();

            // Jika status sudah PAID, jangan diubah lagi
            if ($existing && $existing->status === 'paid') {
                $countPaid++;
                continue;
            }

            // Hitung gaji menggunakan salary service
            $result = $salaryService->calculateMonthlySalary($pegawai, $periodeRaw);

            if ($result['status'] === 'success') {
                Penggajian::updateOrCreate(
                    [
                        'id_pegawai' => $pegawai->id_pegawai,
                        'periode'    => $periodeLabel,
                    ],
                    [
                        'gaji_pokok'       => $result['gaji_pokok'],
                        'total_tunjangan'  => $result['tunjangan']['total'],
                        'total_potongan'   => $result['potongan']['non_pajak'],
                        'lembur'           => $result['lembur']['total_nominal'],
                        'pajak_pph21'      => $result['pajak_pph21'],
                        'gaji_bersih'      => $result['gaji_bersih'],
                        'tanggal_transfer' => $tanggalTransfer,
                        'status'           => $existing ? $existing->status : 'pending', // Pertahankan status jika sudah ada (kecuali di-override)
                    ]
                );
                
                if ($existing) {
                    $countUpdated++;
                } else {
                    $countBerhasil++;
                }
            } else {
                $countGagal++;
            }
        }

        $dept = $request->filled('departemen_id')
            ? \App\Models\Departemen::find($request->departemen_id)?->nama_departemen ?? 'Departemen'
            : 'Semua Departemen';

        $totalProses = $countBerhasil + $countUpdated;
        $msg = "Penggajian {$periodeLabel} ({$dept}): Total {$totalProses} pegawai berhasil diproses";
        
        if ($countUpdated > 0) $msg .= " ({$countUpdated} dihitung ulang)";
        if ($countPaid > 0) $msg .= ", {$countPaid} dilewati karena sudah LUNAS";
        if ($countGagal > 0) $msg .= ", {$countGagal} gagal";
        $msg .= ". Transfer dijadwalkan: " . Carbon::parse($tanggalTransfer)->format('d/m/Y');

        return redirect()
            ->route('officers.penggajian.index', [
                'periode'       => $periodeRaw,
                'departemen_id' => $request->departemen_id
            ])
            ->with('success', $msg);
    }

    /**
     * Update status penggajian menjadi PAID.
     */
    public function updateStatus(Request $request, $id)
    {
        $penggajian = Penggajian::findOrFail($id);
        $penggajian->update(['status' => 'paid']);

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui menjadi PAID.');
    }

    /**
     * Update status massal menjadi PAID untuk filter yang sedang aktif.
     */
    public function bulkPay(Request $request)
    {
        $query = Penggajian::where('status', 'pending');

        if ($request->filled('periode')) {
            $periodeLabel = $this->toPeriodeLabel($request->periode);
            $query->where('periode', $periodeLabel);
        }

        if ($request->filled('departemen_id')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen_id);
            });
        }

        $count = $query->count();
        $query->update(['status' => 'paid']);

        return redirect()->back()->with('success', "Berhasil! {$count} data penggajian telah diperbarui menjadi PAID.");
    }
}
