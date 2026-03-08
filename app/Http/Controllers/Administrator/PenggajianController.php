<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Administrator/PenggajianController
 * Super Admin hanya MELIHAT data penggajian saja.
 * Hitung Gaji adalah tugas HR Officer.
 */
class PenggajianController extends Controller
{
    /** Nama bulan Indonesia */
    private static array $bulanId = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
    ];

    private function toPeriodeLabel(string $periodeRaw): string
    {
        $dt = Carbon::createFromFormat('Y-m', $periodeRaw);
        return self::$bulanId[(int)$dt->format('n')] . ' ' . $dt->format('Y');
    }
    /**
     * Tampilkan daftar penggajian dengan filter periode.
     * Jika periode dipilih, tampilkan data bulan itu saja.
     * Jika tidak ada filter, tampilkan SEMUA data (tanpa pesan kosong).
     */
    public function index(Request $request)
    {
        $query = Penggajian::with(['pegawai', 'pegawai.jabatan', 'pegawai.departemen']);

        // Filter berdasarkan periode yang dipilih (format: "2026-01" → "Januari 2026")
        $periodeFilter = null;
        $periodeLabel  = null;

        if ($request->filled('periode')) {
            $periodeRaw   = $request->periode; // "2026-01"
            $periodeLabel = $this->toPeriodeLabel($periodeRaw);
            $query->where('periode', $periodeLabel);
            $periodeFilter = $periodeRaw;
        }

        $penggajian = $query->orderBy('created_at', 'desc')->get();

        return view('administrator.penggajian.index', compact('penggajian', 'periodeFilter', 'periodeLabel'));
    }

    /**
     * Detail slip gaji.
     */
    public function show($id)
    {
        $penggajian = Penggajian::with(['pegawai', 'pegawai.jabatan', 'pegawai.departemen'])->findOrFail($id);
        return view('administrator.penggajian.show', compact('penggajian'));
    }
}
