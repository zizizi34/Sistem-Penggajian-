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
 * HR Officer bertanggung jawab menghitung & melihat penggajian departemennya.
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
     * Tampilkan daftar penggajian departemen, dengan filter bulan.
     */
    public function index(Request $request)
    {
        $officer      = auth('officer')->user();
        $departemenId = $officer->id_departemen;

        $query = Penggajian::whereHas('pegawai', function ($q) use ($departemenId) {
            $q->where('id_departemen', $departemenId);
        })->with(['pegawai', 'pegawai.jabatan']);

        // Filter berdasarkan periode
        $periodeFilter = null;
        $periodeLabel  = null;

        if ($request->filled('periode')) {
            $periodeRaw   = $request->periode; // "2026-01"
            $periodeLabel = $this->toPeriodeLabel($periodeRaw);
            $query->where('periode', $periodeLabel);
            $periodeFilter = $periodeRaw;
        }

        $penggajian = $query->orderBy('created_at', 'desc')->get();

        return view('officer.penggajian.index', compact('penggajian', 'periodeFilter', 'periodeLabel'));
    }

    /**
     * Detail penggajian.
     */
    public function show($id)
    {
        $officer      = auth('officer')->user();
        $departemenId = $officer->id_departemen;

        $penggajian = Penggajian::whereHas('pegawai', function ($q) use ($departemenId) {
            $q->where('id_departemen', $departemenId);
        })->with(['pegawai', 'pegawai.jabatan', 'pegawai.departemen'])->findOrFail($id);

        return view('officer.penggajian.show', compact('penggajian'));
    }

    /**
     * Hitung dan simpan penggajian bulanan untuk DEPARTEMEN sendiri.
     * Tanggal transfer ditetapkan di akhir bulan yang dipilih.
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'periode' => 'required|regex:/^\d{4}-\d{2}$/',
        ]);

        $officer      = auth('officer')->user();
        $departemenId = $officer->id_departemen;
        $periodeRaw   = $request->periode;

        // Label tampilan Indonesia: "Januari 2026"
        $periodeLabel    = $this->toPeriodeLabel($periodeRaw);
        $tanggalTransfer = Carbon::createFromFormat('Y-m', $periodeRaw)->endOfMonth()->format('Y-m-d');

        $salaryService = app(SalaryCalculationService::class);

        // Ambil pegawai aktif di departemen ini
        $pegawais = Pegawai::where('status_pegawai', 'aktif')
            ->where('id_departemen', $departemenId)
            ->get();

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

            // Hitung gaji
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

        $msg = "Penggajian {$periodeLabel}: {$countBerhasil} pegawai berhasil dihitung";
        if ($countExist > 0) $msg .= ", {$countExist} sudah ada";
        if ($countGagal > 0) $msg .= ", {$countGagal} gagal";
        $msg .= ". Transfer dijadwalkan: " . Carbon::parse($tanggalTransfer)->format('d/m/Y');

        return redirect()
            ->route('officers.penggajian.index', ['periode' => $periodeRaw])
            ->with('success', $msg);
    }
}
