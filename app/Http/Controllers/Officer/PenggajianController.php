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

        return view('officer.penggajian.show', compact('penggajian'));
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
        $tanggalTransfer = Carbon::createFromFormat('Y-m', $periodeRaw)->endOfMonth()->format('Y-m-d');

        $salaryService = app(SalaryCalculationService::class);

        // Ambil pegawai aktif — semua departemen, atau filter departemen tertentu
        $query = Pegawai::where('status_pegawai', 'aktif');
        if ($request->filled('departemen_id')) {
            $query->where('id_departemen', $request->departemen_id);
        }
        $pegawais = $query->get();

        $countBerhasil = 0;
        $countExist    = 0;
        $countGagal    = 0;

        foreach ($pegawais as $pegawai) {
            // Cek apakah sudah ada untuk periode ini
            $existing = Penggajian::where('id_pegawai', $pegawai->id_pegawai)
                ->where('periode', $periodeLabel)
                ->exists();

            if ($existing) {
                $countExist++;
                continue;
            }

            // Hitung gaji menggunakan salary service
            $result = $salaryService->calculateMonthlySalary($pegawai, $periodeRaw);

            if ($result['status'] === 'success') {
                Penggajian::create([
                    'id_pegawai'       => $pegawai->id_pegawai,
                    'periode'          => $periodeLabel,
                    'gaji_pokok'       => $result['gaji_pokok'],
                    'total_tunjangan'  => $result['tunjangan']['total'],
                    'total_potongan'   => $result['potongan']['non_pajak'],
                    'lembur'           => $result['lembur']['total_nominal'],
                    'pajak_pph21'      => $result['pajak_pph21'],
                    'gaji_bersih'      => $result['gaji_bersih'],
                    'tanggal_transfer' => $tanggalTransfer,
                    'status'           => 'pending',
                ]);
                $countBerhasil++;
            } else {
                $countGagal++;
            }
        }

        $dept = $request->filled('departemen_id')
            ? \App\Models\Departemen::find($request->departemen_id)?->nama_departemen ?? 'Departemen'
            : 'Semua Departemen';

        $msg = "Penggajian {$periodeLabel} ({$dept}): {$countBerhasil} pegawai berhasil dihitung";
        if ($countExist > 0) $msg .= ", {$countExist} sudah ada";
        if ($countGagal > 0) $msg .= ", {$countGagal} gagal";
        $msg .= ". Transfer dijadwalkan: " . Carbon::parse($tanggalTransfer)->format('d/m/Y');

        return redirect()
            ->route('officers.penggajian.index', ['periode' => $periodeRaw])
            ->with('success', $msg);
    }
}
